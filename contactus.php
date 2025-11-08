<?php
  $title = "Contact Us | Medi2Home";
  require "./template/header.php";
?>

<style>
  body {
    background-color: #e3f2f4;
  }

  .contact-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 90vh;
    padding-top: 2rem;
    padding-bottom: 3rem;
  }

  .contact-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    flex-wrap: wrap;
    max-width: 900px;
    overflow: hidden;
    width: 100%;
    animation: fadeInUp 0.6s ease-out;
  }

  .contact-left {
    flex: 1 1 45%;
    background: #f8fafb;
    padding: 2rem;
    border-right: 2px solid #eee;
  }

  .contact-right {
    flex: 1 1 55%;
    padding: 2rem;
  }

  .contact-left h5,
  .contact-right h5 {
    font-weight: 600;
    color: #198754;
  }

  .contact-left p {
    margin: 0;
    color: #555;
  }

  .contact-icon {
    color: #6c63ff;
    margin-right: 10px;
    font-size: 1.2rem;
  }

  .btn-purple {
    background-color: #4B3EFF;
    color: white;
    border: none;
    border-radius: 8px;
    transition: 0.3s ease-in-out;
  }

  .btn-purple:hover {
    background-color: #3a2eea;
    transform: translateY(-2px);
  }

  @keyframes fadeInUp {
    0% { transform: translateY(20px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
  }

  @media (max-width: 768px) {
    .contact-card {
      flex-direction: column;
    }
    .contact-left {
      border-right: none;
      border-bottom: 2px solid #eee;
    }
  }
</style>

<div class="contact-container">
  <div class="contact-card">
    <!-- Left Side: Contact Details -->
    <div class="contact-left">
      <h5><i class="bi bi-geo-alt-fill contact-icon"></i> Address</h5>
      <p>22-25-334<br>Beside Gandhi Statue,<br>Mysore</p>

      <hr>

      <h5><i class="bi bi-telephone-fill contact-icon"></i> Phone</h5>
      <p>+91 9893 5647<br>+91 9434 5678</p>

      <hr>

      <h5><i class="bi bi-envelope-fill contact-icon"></i> Email</h5>
      <p>
        medi2home@gmail.com<br>
        medi2home.manager@gmail.com
      </p>
    </div>

    <!-- Right Side: Message Section -->
    <div class="contact-right">
      <h4 class="text-primary mb-3"><i class="bi bi-chat-dots-fill"></i> Send Us a Message</h4>
      <p>
        If you need a medicine that’s not available on our website, please email us —
        we’ll add it within <strong>24 hours!</strong>
      </p>

      <div class="mt-4">
        <a href="mailto:medi2home@gmail.com" class="btn btn-purple px-4 me-2">
          <i class="bi bi-send-fill"></i> Send Email
        </a>
        <a href="index.php" class="btn btn-outline-success px-4">
          <i class="bi bi-house-door"></i> Back to Home
        </a>
      </div>
    </div>
  </div>
</div>

<?php require "./template/footer.php"; ?>
