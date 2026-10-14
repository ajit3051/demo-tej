<style>
/* Blur Background */
.custom-popup-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    /*backdrop-filter: blur(6px);*/
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

/* Popup Box */
.custom-popup {
    background: #ffffff;
    padding: 30px 40px;
    border-radius: 18px;
    text-align: center;
    width: 350px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: zoomIn 0.3s ease;
    position: relative;
}

/* Icon Circle */
.popup-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg,#00c853,#009688);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: -65px auto 20px auto;
    color: white;
    font-size: 30px;
    box-shadow: 0 10px 20px rgba(0,150,136,0.4);
}

/* Button */
.popup-btn {
    background: linear-gradient(135deg,#00c853,#009688);
    border: none;
    padding: 10px 25px;
    color: white;
    border-radius: 30px;
    cursor: pointer;
    font-weight: 600;
    margin-top: 15px;
    transition: 0.3s;
}

.popup-btn:hover {
    transform: scale(1.05);
}

/* Animations */
@keyframes zoomIn {
    from { transform: scale(0.7); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>