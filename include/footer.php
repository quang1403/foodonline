
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<footer class="footer-modern">
  <div class="container py-4">
    <div class="footer-grid">
      <div class="footer-section payment">
        <h5 class="footer-title">Phương thức thanh toán</h5>
        <div class="payment-icons">
          <img src="images/paypal.png" alt="Paypal" class="pay-icon" />
          <img src="images/mastercard.png" alt="Mastercard" class="pay-icon" />
          <img src="images/maestro.png" alt="Maestro" class="pay-icon" />
          <img src="images/stripe.png" alt="Stripe" class="pay-icon" />
          <img src="images/bitcoin.png" alt="Bitcoin" class="pay-icon" />
        </div>
      </div>
      <div class="footer-section address">
        <h5 class="footer-title">Địa chỉ</h5>
        <p>Tòa nhà TC, Ba Đình, Hà Nội</p>
        <p>Phone: +123456</p>
      </div>
      <div class="footer-section contact">
        <h5 class="footer-title">Liên hệ với chúng tôi</h5>
        <p>Email: onlinefoodhq@gmail.com</p>
        <p>Website: onlinefood.com</p>
        <div class="footer-social">
          <a href="#" class="social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
        <p>Phone: +123456</p>
      </div>
    </div>
    <div class="footer-copyright mt-4">
      <p>© 2025 OnlineFood. All rights reserved.</p>
    </div>
  </div>
</footer>

<style>
.footer-modern {
  background: #f7f8fa;
  color: #222;
  font-family: 'Segoe UI', Arial, sans-serif;
  border-top: 1px solid #e0e0e0;
  margin-top: 40px;
}
.footer-modern .container {
  max-width: 1200px;
}
.footer-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  justify-content: space-between;
}
.footer-section {
  flex: 1 1 220px;
  min-width: 220px;
  padding: 0 8px;
}
.footer-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 16px;
  color: #2d9cdb;
}
.payment-icons {
  display: flex;
  gap: 16px;
  align-items: center;
}
.pay-icon {
  width: 40px;
  height: 40px;
  object-fit: contain;
  filter: grayscale(0.1) brightness(1);
  transition: transform 0.2s, box-shadow 0.2s;
  border-radius: 8px;
  background: #fff;
  padding: 6px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.pay-icon:hover {
  transform: scale(1.08);
  box-shadow: 0 2px 8px rgba(45,156,219,0.12);
}
.footer-section p {
  margin: 0 0 8px 0;
  font-size: 0.98rem;
}
.footer-social {
  margin: 10px 0 8px 0;
  display: flex;
  gap: 16px;
}
.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: #e9ecef;
  border-radius: 50%;
  color: #2d9cdb;
  font-size: 1.3rem;
  transition: background 0.2s, color 0.2s, transform 0.2s;
  text-decoration: none;
}
.social-icon:hover {
  background: #2d9cdb;
  color: #fff;
  transform: scale(1.1);
}
.footer-copyright {
  text-align: center;
  font-size: 0.98rem;
  color: #888;
  border-top: 1px solid #e0e0e0;
  padding-top: 16px;
}
@media (max-width: 900px) {
  .footer-grid {
    flex-direction: column;
    gap: 24px;
  }
  .footer-section {
    min-width: 0;
    padding: 0;
  }
}
</style>
