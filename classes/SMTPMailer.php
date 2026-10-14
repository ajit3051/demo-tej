<?php
class SMTPMailer {
    private $smtp_host;
    private $smtp_port;
    private $smtp_user;
    private $smtp_pass;
    private $secure_type;
    private $timeout;
    private $socket;
    private $newline = "\r\n";

    // Configurable parameters
    public $from;
    public $to;
    public $cc = []; // CC recipients
    public $bcc = []; // BCC recipients
    public $subject;
    public $body;
    public $attachments = [];

    public function __construct($host, $port, $user, $pass, $secure_type = 'tls', $timeout = 30) {
        $this->smtp_host = $host;
        $this->smtp_port = $port;
        $this->smtp_user = $user;
        $this->smtp_pass = $pass;
        $this->secure_type = $secure_type;
        $this->timeout = $timeout;
    }

    private function connect()
{
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
        ],
    ]);

    $protocol = $this->secure_type === 'ssl' ? 'ssl://' : 'tcp://';
    $this->socket = stream_socket_client(
        "{$protocol}{$this->smtp_host}:{$this->smtp_port}",
        $errno,
        $errstr,
        $this->timeout,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if (!$this->socket) {
        throw new Exception("Could not connect to SMTP host: $errstr ($errno)");
    }

    $this->readResponse(220);
    $this->sendCommand("EHLO " . $this->smtp_host, 250);

    // Start TLS if secure_type is 'tls'
    if ($this->secure_type === 'tls') {
        $this->sendCommand("STARTTLS", 220);

        // Upgrade the socket to use encryption
        if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception("Failed to enable TLS encryption.");
        }

        // Reintroduce EHLO after STARTTLS
        $this->sendCommand("EHLO " . $this->smtp_host, 250);
    }
}


    private function authenticate() {
        $this->sendCommand("AUTH LOGIN", 334);
        $this->sendCommand(base64_encode($this->smtp_user), 334);
        $this->sendCommand(base64_encode($this->smtp_pass), 235);
    }

    public function send() {
        if (empty($this->from) || empty($this->to) || empty($this->subject) || empty($this->body)) {
            throw new Exception("Email 'from', 'to', 'subject', and 'body' fields must be set.");
        }

        $this->connect();
        $this->authenticate();

        $boundary = md5(time());

        // Prepare headers
        $headers = "From: {$this->from}" . $this->newline;
        $headers .= "To: {$this->to}" . $this->newline;

        // Add CC and BCC headers if provided
        if (!empty($this->cc)) {
            $headers .= "CC: " . implode(", ", $this->cc) . $this->newline;
        }
        if (!empty($this->bcc)) {
            $headers .= "BCC: " . implode(", ", $this->bcc) . $this->newline;
        }

        $headers .= "Subject: {$this->subject}" . $this->newline;
        $headers .= "MIME-Version: 1.0" . $this->newline;
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"" . $this->newline;

        // Email body
        $message = "--{$boundary}" . $this->newline;
        $message .= "Content-Type: text/html; charset=UTF-8" . $this->newline;
        $message .= "Content-Transfer-Encoding: 7bit" . $this->newline . $this->newline;
        $message .= $this->body . $this->newline . $this->newline;

        // Attachments
        foreach ($this->attachments as $attachment) {
            $filePath = $attachment['path'];
            $fileName = $attachment['name'];

            if (file_exists($filePath)) {
                $fileData = file_get_contents($filePath);
                $fileData = chunk_split(base64_encode($fileData));

                $message .= "--{$boundary}" . $this->newline;
                $message .= "Content-Type: application/octet-stream; name=\"{$fileName}\"" . $this->newline;
                $message .= "Content-Transfer-Encoding: base64" . $this->newline;
                $message .= "Content-Disposition: attachment; filename=\"{$fileName}\"" . $this->newline . $this->newline;
                $message .= $fileData . $this->newline . $this->newline;
            }
        }

        $message .= "--{$boundary}--";

        // SMTP commands to send email
        $this->sendCommand("MAIL FROM: <{$this->from}>", 250);
        $this->sendCommand("RCPT TO: <{$this->to}>", 250);
        
        // Add recipients in CC and BCC
        foreach ($this->cc as $ccRecipient) {
            $this->sendCommand("RCPT TO: <{$ccRecipient}>", 250);
        }
        foreach ($this->bcc as $bccRecipient) {
            $this->sendCommand("RCPT TO: <{$bccRecipient}>", 250);
        }

        $this->sendCommand("DATA", 354);
        $this->sendCommand($headers . $this->newline . $message . $this->newline . ".", 250);

        $this->sendCommand("QUIT", 221);
        fclose($this->socket);
    }

    private function sendCommand($command, $expectedCode) {
        fputs($this->socket, $command . $this->newline);
        $this->readResponse($expectedCode);
    }

    private function readResponse($expectedCode) {
        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= trim($line) . "\n";
            if (preg_match('/^\d{3} /', $line)) {
                break;
            }
        }

        $statusCode = (int) substr($response, 0, 3);
        if ($statusCode !== $expectedCode) {
            throw new Exception("SMTP Error: $response");
        }
    }
}
