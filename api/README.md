# Tejas ERP - RESTful API Documentation for Frontend Integration

**Base URL**: `http://localhost/demo-tej/api`  
**Alternative Direct URL Format**: `http://localhost/demo-tej/api/<module>.php?action=<action>`  
**Default Content-Type**: `application/json` (also supports `application/x-www-form-urlencoded` / `multipart/form-data`)

---

## 1. Global Standards & Conventions

### Response Envelope
All API endpoints return a standardized JSON envelope:

#### Success Response (`200 OK`, `201 Created`)
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... } or [ ... ],
  "meta": {
    "pagination": {
      "total_records": 100,
      "current_page": 1,
      "per_page": 20,
      "total_pages": 5,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

#### Error Response (`400 Bad Request`, `401 Unauthorized`, `404 Not Found`, `422 Unprocessable`)
```json
{
  "success": false,
  "message": "Detailed error message describing the issue",
  "errors": {
    "field_name": "Field validation failure description"
  }
}
```

### Authentication Header
For protected endpoints, include the Bearer token in the `Authorization` header:
```http
Authorization: Bearer <your_jwt_token>
```
*(Alternatively, pass `?token=<your_jwt_token>` as a query parameter or `X-Auth-Token: <token>` header)*.

---

## 2. Authentication Module

### 2.1 User Login
Authenticates user credentials and returns a secure JWT Bearer token along with user profile and assigned store ID.

> [!NOTE]
> **API Login Requirement**: Only users with `user_type = 'api'` (case-insensitive) are permitted to authenticate via the API. Non-API accounts (e.g. `user_type = 'Admin'` or `'Power User'`) will receive a `403 Forbidden` response.

- **URL**: `/auth/login` (or `/auth.php?action=login`)
- **Method**: `POST`
- **Headers**: `Content-Type: application/json`
- **Request Payload**:
```json
{
  "username": "api_user",
  "password": "your_password"
}
```
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_at": 1762441200,
    "user": {
      "id": 1,
      "username": "admin",
      "full_name": "Tejas Admin",
      "email": "admin@demo.com",
      "mobile_no": "9876543210",
      "user_role": "admin",
      "store_id": 35,
      "status": "active"
    }
  }
}
```
- **Error Response (`401 Unauthorized`)**:
```json
{
  "success": false,
  "message": "Invalid username or password."
}
```

---

### 2.2 Get Current User Profile
Retrieves currently logged-in user profile from Bearer token.

- **URL**: `/auth/me` (or `/auth.php?action=me`)
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Payload**: None
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Authenticated user profile.",
  "data": {
    "id": 1,
    "username": "admin",
    "full_name": "Tejas Admin",
    "email": "admin@demo.com",
    "mobile_no": "9876543210",
    "user_role": "admin",
    "store_id": 35,
    "status": "active"
  }
}
```

---

### 2.3 User Logout
Invalidates the current session/token.

- **URL**: `/auth/logout` (or `/auth.php?action=logout`)
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`
- **Payload**: None
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Logged out successfully."
}
```

---

### 2.4 Change Password
Updates user password after verifying the old password.

- **URL**: `/auth/change_password` (or `/auth.php?action=change_password`)
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`
- **Request Payload**:
```json
{
  "old_password": "current_password",
  "new_password": "new_secure_password"
}
```
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Password changed successfully."
}
```

---

## 3. Dashboard & Analytics Module

### 3.1 Executive Dashboard Summary
Provides KPI counts, today's sales/purchases, payment method breakdown (Cash, UPI, Card, Credit), inventory summary, low-stock items, and recent 5 transactions.

- **URL**: `/dashboard` (or `/dashboard.php?action=summary`)
- **Method**: `GET`
- **Payload**: None
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Dashboard summary retrieved successfully",
  "data": {
    "sales": {
      "today": {
        "date": "2026-11-05",
        "bills": 1,
        "amount": 1000,
        "cash": 1000,
        "upi": 0,
        "card": 0,
        "credit": 0
      },
      "month": {
        "bills": 4,
        "amount": 2500
      },
      "all_time": {
        "bills": 22,
        "amount": 115059.6
      }
    },
    "purchases": {
      "today": { "bills": 0, "amount": 0 },
      "month": { "bills": 0, "amount": 0 }
    },
    "counts": {
      "total_items": 5,
      "total_customers": 3,
      "total_vendors": 0,
      "total_stock_units": 34
    },
    "low_stock_items": [
      {
        "id": 8,
        "item_code": "IC-0008",
        "item_name": "demo item",
        "barcode": "10000001",
        "current_stock": "0"
      }
    ],
    "recent_sales": [
      {
        "bill_no": "BC-0022",
        "bill_date": "2026-11-05",
        "customer_name": "Rishabh Kesharwani",
        "customer_mobile": "1234567898",
        "bill_amount": "1000.00",
        "paymode": "CASH",
        "created_at": "2026-11-05 17:16:37"
      }
    ],
    "recent_purchases": []
  }
}
```

---

## 4. Sales & POS Billing Module

### 4.1 Recent Sales (Day-wise / POS View)
Used for the POS "Recently Sales / Bills" pane. Filters by day so recent bills are instantly accessible without cluttering.

- **URL**: `/sales/recent` (or `/sales.php?action=recent`)
- **Method**: `GET`
- **Query Parameters**:
  - `filter_date` *(optional)*: Date in `YYYY-MM-DD` (defaults to today `date('Y-m-d')`).
  - `search` *(optional)*: Search by bill number, customer name, or mobile number.
  - `page` *(optional, default: 1)*: Page number.
  - `limit` *(optional, default: 15)*: Page size.
- **Example Request**: `GET /sales/recent?filter_date=2026-11-05&limit=10`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Recent sales retrieved.",
  "data": [
    {
      "id": 27,
      "purchage_date": "2026-11-05 00:00:00",
      "bill_number": "BC-0022",
      "account_name": "Rishabh Kesharwani",
      "mobile_number": "1234567898",
      "payment_mode": "CASH",
      "items_count": 1,
      "total_qty": 2,
      "total_bill_amount": "1000.00",
      "pay_cash": "1000.00",
      "pay_upi": "0.00",
      "pay_card": "0.00",
      "pay_credit": "0.00"
    }
  ],
  "meta": {
    "filter_date": "2026-11-05",
    "pagination": {
      "total_records": 1,
      "current_page": 1,
      "per_page": 10,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  }
}
```

---

### 4.2 Get Next Sequential Bill Number
Generates the next sequential bill number (e.g., `BC-0023`).

- **URL**: `/sales/next_bill_no` (or `/sales.php?action=next_bill_no`)
- **Method**: `GET`
- **Payload**: None
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Next bill number generated.",
  "data": {
    "bill_no": "BC-0023"
  }
}
```

---

### 4.3 Get Bill Details (Header + Items)
Fetches full details of a single bill including all purchased item rows.

- **URL**: `/sales/details` (or `/sales.php?action=details`)
- **Method**: `GET`
- **Query Parameters**:
  - `bill_no`: Bill number (e.g. `BC-0022`)
- **Example Request**: `GET /sales/details?bill_no=BC-0022`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Bill details retrieved.",
  "data": {
    "bill_header": {
      "bill_number": "BC-0022",
      "bill_date": "05-11-2026",
      "customer_name": "Rishabh Kesharwani",
      "customer_mobile": "1234567898",
      "payment_mode": "CASH",
      "pay_cash": "1000.00",
      "pay_upi": "0.00",
      "pay_card": "0.00",
      "pay_credit": "0.00",
      "grand_total": "1000.00"
    },
    "items": [
      {
        "id": 27,
        "barcode_number": "12121",
        "item_name": "shoes",
        "category": "Ledger LegendS",
        "size": "28",
        "color": "",
        "qty": 2,
        "rate": "500.00",
        "purchase_price": "12.00",
        "percent_discount": "0.00",
        "discount_amt": "0.00",
        "net_price": "500.00",
        "taxable_amount": "1000.00",
        "gst": "0",
        "gst_amount": "0.00",
        "amount": "1000.00"
      }
    ]
  }
}
```

---

### 4.4 Create Sale / Process Bill (POS Checkout)
Saves a complete bill with multiple items and updates store stock in a single atomic database transaction.

- **URL**: `/sales/create` (or `/sales.php?action=create`)
- **Method**: `POST`
- **Headers**: `Content-Type: application/json`
- **Request Payload**:
```json
{
  "bill_number": "BC-0023",
  "purchage_date": "2026-11-05",
  "account_name": "Rishabh Kesharwani",
  "mobile_number": "9876543210",
  "gst_type": "Inclusive",
  "under_gst": "Local",
  "bill_type": "Tax-Bill",
  "pay_mode": "CASH",
  "pay_cash": 1000,
  "pay_upi": 0,
  "pay_card": 0,
  "pay_credit": 0,
  "store_name": "tejas store-01",
  "s_code": "SC35",
  "remarks": "Walk-in retail customer",
  "items": [
    {
      "barcode_number": "12121",
      "item_name": "shoes",
      "category": "Ledger LegendS",
      "brand": "",
      "color": "",
      "size": "28",
      "qty": 2,
      "rate": 500,
      "purchase_price": 12,
      "percent_discount": 0,
      "discount_amt": 0,
      "net_price": 500,
      "taxable_amount": 1000,
      "gst": 0,
      "gst_amount": 0,
      "amount": 1000
    }
  ]
}
```
- **Success Response (`201 Created`)**:
```json
{
  "success": true,
  "message": "Bill created successfully.",
  "data": {
    "bill_number": "BC-0023",
    "items_count": 1,
    "grand_total": 1000
  }
}
```

---

### 4.5 Daily Sales Report - Option 1: DSR-1 (Date-wise Summary)
Aggregates sales by date, providing total quantity, payment breakdowns (Cash, UPI, Card, Credit), taxable rate, total GST, and net amount, along with payment mode summary cards (CASH, UPI, CARD, CREDIT).

- **URL**: `/sales/dsr1` (or `/sales.php?action=dsr1` or `/sales.php?action=dsr&type=dsr1`)
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Query Parameters**:
  - `filter_date` *(optional)*: Single date (`YYYY-MM-DD`, e.g. `2026-11-05`)
  - `from_date` *(optional)*: Range start (`YYYY-MM-DD`)
  - `to_date` *(optional)*: Range end (`YYYY-MM-DD`)
  - `recent` *(optional)*: `1` for today only
  - `search` *(optional)*: Bill number, customer name, barcode, or item name
  - `page` *(optional, default: 1)*
  - `limit` *(optional, default: 20)*
- **Example Request**: `GET /sales/dsr1?filter_date=2026-11-05`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Daily Sales Report (DSR-1) retrieved successfully.",
  "data": {
    "report_type": "DSR-1 (Date-wise Summary)",
    "items": [
      {
        "purchage_date": "2026-11-05",
        "purchage_date_formatted": "05-11-2026",
        "total_qty": "2",
        "total_cash": "1000.00",
        "total_upi": "0.00",
        "total_card": "0.00",
        "total_credit": "0.00",
        "total_rate": "1000.00",
        "total_gst": "0.00",
        "total_amount": "1000.00"
      }
    ],
    "payment_cards": [
      { "payment_mode": "CASH", "total_qty": "36", "total_amount": "102940.92" },
      { "payment_mode": "UPI", "total_qty": "1", "total_amount": "5608.96" },
      { "payment_mode": "CARD", "total_qty": "1", "total_amount": "5608.96" },
      { "payment_mode": "CREDIT", "total_qty": "0", "total_amount": "0.00" }
    ],
    "summary": {
      "total_qty": 38,
      "total_rate": 103002,
      "total_gst": 12057.6,
      "total_amount": 115059.6,
      "total_cash": 102940.92,
      "total_upi": 5608.96,
      "total_card": 5608.96,
      "total_credit": 0
    },
    "pagination": {
      "total_records": 10,
      "current_page": 1,
      "per_page": 20,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  }
}
```

---

### 4.6 Daily Sales Report - Option 2: DSR-2 (Item-wise Detailed Report)
Provides item-level sales details for each transaction line (bill number, customer, barcode, item name, size, color, quantity, mrp, rate, taxable amount, gst, net amount, and payment mode breakdown), along with the payment mode summary cards.

- **URL**: `/sales/dsr2` (or `/sales.php?action=dsr2` or `/sales.php?action=dsr&type=dsr2`)
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Query Parameters**:
  - `filter_date` *(optional)*: Single date (`YYYY-MM-DD`)
  - `from_date` *(optional)*: Range start (`YYYY-MM-DD`)
  - `to_date` *(optional)*: Range end (`YYYY-MM-DD`)
  - `recent` *(optional)*: `1` for today only
  - `search` *(optional)*: Keyword
  - `page` *(optional, default: 1)*
  - `limit` *(optional, default: 20)*
- **Example Request**: `GET /sales/dsr2?filter_date=2026-11-05`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Daily Sales Report (DSR-2) retrieved successfully.",
  "data": {
    "report_type": "DSR-2 (Item-wise Detailed)",
    "items": [
      {
        "id": 27,
        "bill_number": "BC-0022",
        "purchage_date": "2026-11-05 00:00:00",
        "purchage_date_formatted": "05-11-2026",
        "account_name": "Rishabh Kesharwani",
        "mobile_number": "1234567898",
        "barcode_number": "12121",
        "item_name": "shoes",
        "size": "28",
        "color": "",
        "qty": 2,
        "rate": "500.00",
        "taxable_amount": "1000.00",
        "gst": "0.00",
        "gst_amount": "0.00",
        "amount": "1000.00",
        "pay_mode": "CASH",
        "pay_cash": "1000.00",
        "pay_upi": "0.00",
        "pay_card": "0.00",
        "pay_credit": "0.00"
      }
    ],
    "payment_cards": [
      { "payment_mode": "CASH", "total_qty": "36", "total_amount": "102940.92" },
      { "payment_mode": "UPI", "total_qty": "1", "total_amount": "5608.96" },
      { "payment_mode": "CARD", "total_qty": "1", "total_amount": "5608.96" },
      { "payment_mode": "CREDIT", "total_qty": "0", "total_amount": "0.00" }
    ],
    "summary": {
      "total_qty": 38,
      "total_rate": 103002,
      "total_gst": 12057.6,
      "total_amount": 115059.6
    },
    "pagination": {
      "total_records": 23,
      "current_page": 1,
      "per_page": 20,
      "total_pages": 2,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

---

### 4.7 Delete Bill
Deletes a bill and all its associated item entries.

- **URL**: `/sales/delete` (or `/sales.php?action=delete`)
- **Method**: `POST`
- **Request Payload**:
```json
{
  "bill_no": "BC-0023"
}
```
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Bill BC-0023 deleted successfully."
}
```

---

## 5. Items & POS Barcode Lookup Module

### 5.1 POS Barcode / Term Lookup (Instant Scan)
High-speed lookup endpoint optimized for POS input scanner. Checks both `tbl_item_master` and `tbl_purchase` records and calculates live store stock.

- **URL**: `/items/lookup` (or `/items.php?action=lookup`)
- **Method**: `GET`
- **Query Parameters**:
  - `term`: Barcode, SKU, or Item Code (e.g. `12121`)
- **Example Request**: `GET /items/lookup?term=12121`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Item lookup successful.",
  "data": {
    "barcode_number": "12121",
    "article_no": "12121",
    "item_name": "shoes",
    "category": "Ledger LegendS",
    "brand": "",
    "color": "",
    "size": "28",
    "mou_name": "EA",
    "purchase_price": 12,
    "mrp": 20,
    "sp": 500,
    "rate": 0,
    "gst": 0,
    "stock_qty": 18,
    "source": "purchase_record"
  }
}
```

---

### 5.2 Generate Auto Barcode
Generates the next unique numeric barcode sequence for item creation.

- **URL**: `/items/generate_barcode` (or `/items.php?action=generate_barcode`)
- **Method**: `GET`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Barcode generated successfully.",
  "data": {
    "barcode": "10000003"
  }
}
```

---

### 5.3 Items List & Filter
- **URL**: `/items/list` (or `/items.php?action=list`)
- **Method**: `GET`
- **Query Parameters**: `search`, `category`, `brand`, `page`, `limit`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Items retrieved successfully.",
  "data": [
    {
      "id": 9,
      "item_code": "IC-0009",
      "barcode_no": "12121",
      "sku_no": "SK-0009",
      "item_name": "shoes",
      "group_name": "Ledger LegendS",
      "brand_name": "",
      "size_name": "28",
      "color_name": "",
      "purchase_price": "12",
      "mrp": "20",
      "sp": "500",
      "current_stock": 18
    }
  ],
  "meta": {
    "pagination": {
      "total_records": 5,
      "current_page": 1,
      "per_page": 20,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  }
}
```

---

### 5.4 Create Item
- **URL**: `/items/create` (or `/items.php?action=create`)
- **Method**: `POST`
- **Request Payload**:
```json
{
  "item_code": "IC-0012",
  "barcode_no": "10000003",
  "sku_no": "SK-0012",
  "item_name": "Cotton Casual Shirt",
  "group_name": "Apparel",
  "brand_name": "Nike",
  "color_name": "Blue",
  "size_name": "L",
  "mou_name": "PCS",
  "hsn_code": "6205",
  "gst_per": "5",
  "purchase_price": "450",
  "mrp": "999",
  "sp": "850",
  "min_qty": "5"
}
```
- **Success Response (`201 Created`)**:
```json
{
  "success": true,
  "message": "Item created successfully.",
  "data": {
    "id": 12,
    "item_code": "IC-0012",
    "barcode_no": "10000003",
    "item_name": "Cotton Casual Shirt"
  }
}
```

---

## 6. Accounts & Ledger Module

### 6.1 Autocomplete Account Search
Instant search by mobile number, account name, or code for customer/vendor lookups in POS.

- **URL**: `/accounts/search` (or `/accounts.php?action=search`)
- **Method**: `GET`
- **Query Parameters**:
  - `term`: Search keyword (e.g. `rishabh` or `1234567898`)
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Accounts found.",
  "data": [
    {
      "id": 1,
      "ac_code": "AC0001",
      "account_name": "Rishabh Kesharwani",
      "mobile_no": "1234567898",
      "city": "Delhi",
      "party_type": "Customer",
      "status": "Active"
    }
  ]
}
```

---

### 6.2 Customer Running Ledger Statement
Computes the running ledger balance by matching sales debits and payment receipts.

- **URL**: `/accounts/ledger` (or `/accounts.php?action=ledger`)
- **Method**: `GET`
- **Query Parameters**:
  - `mobile` or `ac_code` or `account_name`
  - `from_date` *(optional)*
  - `to_date` *(optional)*
- **Example Request**: `GET /accounts/ledger?mobile=1234567898`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Account ledger retrieved.",
  "data": {
    "account": {
      "account_name": "Rishabh Kesharwani",
      "mobile_no": "1234567898"
    },
    "entries": [
      {
        "date": "2026-11-05 00:00:00",
        "voucher_no": "BC-0022",
        "type": "Sale Bill",
        "particulars": "Sale Invoice #BC-0022",
        "debit": 1000,
        "credit": 0,
        "balance": 1000
      },
      {
        "date": "2026-11-05 00:00:00",
        "voucher_no": "BC-0022",
        "type": "Payment",
        "particulars": "Payment Received (CASH)",
        "debit": 0,
        "credit": 1000,
        "balance": 0
      }
    ],
    "summary": {
      "total_debit": 1000,
      "total_credit": 1000,
      "closing_balance": 0
    }
  }
}
```

---

### 6.3 Create Account
- **URL**: `/accounts/create` (or `/accounts.php?action=create`)
- **Method**: `POST`
- **Request Payload**:
```json
{
  "account_name": "John Doe",
  "mobile_no": "9876543211",
  "email": "johndoe@gmail.com",
  "party_type": "Customer",
  "address": "Flat 201, Green View",
  "city": "Noida",
  "state": "Uttar Pradesh",
  "pincode": "201301",
  "gst_no": ""
}
```
- **Success Response (`201 Created`)**:
```json
{
  "success": true,
  "message": "Account created successfully.",
  "data": {
    "id": 4,
    "ac_code": "AC0004",
    "account_name": "John Doe",
    "mobile_no": "9876543211"
  }
}
```

---

## 7. Purchases Module

### 7.1 Next Purchase Bill Number
- **URL**: `/purchases/next_bill_no` (or `/purchases.php?action=next_bill_no`)
- **Method**: `GET`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Next purchase bill number generated.",
  "data": {
    "bill_no": "PB-0002"
  }
}
```

---

### 7.2 Create Purchase Bill (Supplier Inward)
Creates a purchase bill and automatically increases store inventory in `tbl_store_items`.

- **URL**: `/purchases/create` (or `/purchases.php?action=create`)
- **Method**: `POST`
- **Request Payload**:
```json
{
  "bill_number": "PB-0002",
  "purchage_date": "2026-11-05",
  "account_name": "ABC Suppliers Ltd",
  "store_name": "tejas store-01",
  "s_code": "SC35",
  "payment_mode": "CASH",
  "invoice_number": "INV-8910",
  "invoice_date": "2026-11-05",
  "items": [
    {
      "barcode_number": "12121",
      "item_name": "shoes",
      "category": "Ledger LegendS",
      "brand": "",
      "color": "",
      "size": "28",
      "qty": 50,
      "rate": 10,
      "purchase_price": 12,
      "mrp": 20,
      "sp": 500,
      "net_price": 12,
      "taxable_amount": 600,
      "amount": 600
    }
  ]
}
```
- **Success Response (`201 Created`)**:
```json
{
  "success": true,
  "message": "Purchase bill created successfully.",
  "data": {
    "bill_number": "PB-0002",
    "items_count": 1,
    "grand_total": 600
  }
}
```

---

## 8. Stock & Inventory Module

### 8.1 Stock Quantity Shortcut Summary
Lists total purchased, total sold, and balance available quantity per barcode.

- **URL**: `/stock/report_shortcut` (or `/stock.php?action=report_shortcut`)
- **Method**: `GET`
- **Query Parameters**: `search`, `page`, `limit`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Stock summary report retrieved.",
  "data": [
    {
      "barcode_number": "12121",
      "item_name": "shoes",
      "category": "Ledger LegendS",
      "brand": "",
      "size": "28",
      "color": "",
      "purchased_qty": 52,
      "sold_qty": 34,
      "balance_qty": 18
    }
  ]
}
```

---

### 8.2 Inter-Store Stock Transfer
Transfers item stock from one store branch to another.

- **URL**: `/stock/transfer` (or `/stock.php?action=transfer`)
- **Method**: `POST`
- **Request Payload**:
```json
{
  "from_store": "tejas store-01",
  "to_store": "STORE10",
  "barcode_number": "12121",
  "qty": 5,
  "transfer_by": "Admin",
  "remarks": "Inter-branch balance transfer"
}
```
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Transferred 5 units of barcode 12121 to STORE10 successfully."
}
```

---

## 9. Universal Master Data Module

Supports complete CRUD for all 23 master entities in Tejas ERP:
`brands`, `groups`, `categories`, `subgroups`, `colors`, `sizes`, `gst`, `paymodes`, `stores`, `store_profiles`, `locations`, `expenses_heads`, `expenses`, `coupons`, `discounts`, `salesmen`, `salesman_targets`, `customer_types`, `party_types`, `styles`, `festivals`, `users`, `company`.

### 9.1 List Master Entities
- **URL**: `/masters?action=entities`
- **Method**: `GET`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Available master entities",
  "data": [
    "brands", "groups", "categories", "subgroups", "colors", "sizes", "gst",
    "paymodes", "stores", "store_profiles", "locations", "expenses_heads",
    "expenses", "coupons", "discounts", "salesmen", "salesman_targets",
    "customer_types", "party_types", "styles", "festivals", "users", "company"
  ]
}
```

---

### 9.2 List Master Records (e.g. Brands, Stores, Paymodes)
- **URL**: `/masters/{entity}/list` (or `/masters.php?entity={entity}&action=list`)
- **Method**: `GET`
- **Query Parameters**:
  - `search` *(optional)*
  - `status` *(optional: 'true' / 'false' or 'active' / 'inactive')*
  - `page` *(optional, default: 1)*
  - `limit` *(optional, default: 50)*
- **Example Request**: `GET /masters/brands/list?limit=5`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Brands list retrieved",
  "data": {
    "items": [
      {
        "id": 22,
        "brand_code": "BC-00022",
        "brand_name": "Adidas",
        "status": "true",
        "picture": ""
      }
    ],
    "pagination": {
      "total_records": 10,
      "current_page": 1,
      "per_page": 5,
      "total_pages": 2,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

---

### 9.3 Get Master Record by ID or Code
- **URL**: `/masters/{entity}/get?id=22` (or `?code=BC-00022`)
- **Method**: `GET`
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Record found",
  "data": {
    "id": 22,
    "brand_code": "BC-00022",
    "brand_name": "Adidas",
    "status": "true"
  }
}
```

---

### 9.4 Create Master Record
- **URL**: `/masters/{entity}/create`
- **Method**: `POST`
- **Request Payload** *(example for `brands`)*:
```json
{
  "brand_code": "BC-00023",
  "brand_name": "Puma",
  "status": "true"
}
```
- **Success Response (`201 Created`)**:
```json
{
  "success": true,
  "message": "Brands created successfully",
  "data": {
    "id": 23,
    "brand_code": "BC-00023",
    "brand_name": "Puma",
    "status": "true"
  }
}
```

---

### 9.5 Update Master Record
- **URL**: `/masters/{entity}/update`
- **Method**: `POST`
- **Request Payload**:
```json
{
  "id": 23,
  "brand_name": "Puma Motorsport",
  "status": "true"
}
```
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Brands updated successfully",
  "data": {
    "id": 23,
    "brand_code": "BC-00023",
    "brand_name": "Puma Motorsport",
    "status": "true"
  }
}
```

---

### 9.6 Delete Master Record
- **URL**: `/masters/{entity}/delete`
- **Method**: `POST`
- **Request Payload**:
```json
{
  "id": 23
}
```
- **Success Response (`200 OK`)**:
```json
{
  "success": true,
  "message": "Brands deleted successfully",
  "data": {
    "id": 23
  }
}
```

---

## 10. Frontend Integration Code Examples

### Fetch Example (JavaScript / TypeScript / React / Vue)
```typescript
const API_BASE = 'http://localhost/demo-tej/api';

// POS Barcode Scanner Handler
async function scanBarcode(barcode: string) {
  try {
    const response = await fetch(`${API_BASE}/items/lookup?term=${encodeURIComponent(barcode)}`);
    const result = await response.json();
    if (result.success) {
      console.log('Item found:', result.data);
      return result.data;
    } else {
      alert(result.message);
    }
  } catch (error) {
    console.error('Scan error:', error);
  }
}

// Complete POS Checkout
async function checkoutSale(salePayload: any, token: string) {
  try {
    const response = await fetch(`${API_BASE}/sales/create`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(salePayload)
    });
    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Checkout failed:', error);
  }
}
```

### Axios Example (React / Angular / Vue)
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost/demo-tej/api',
  headers: {
    'Content-Type': 'application/json'
  }
});

// Interceptor to attach Bearer token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('erp_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Load Dashboard Data
export async function getDashboardData() {
  const { data } = await api.get('/dashboard');
  return data;
}

// Load Recent Sales for POS
export async function getRecentSales(filterDate = '') {
  const { data } = await api.get('/sales/recent', {
    params: { filter_date: filterDate }
  });
  return data;
}
```

