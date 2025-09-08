<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
      Note.CSE
    </title>

    <link
      rel="stylesheet"
      href="https://unpkg.com/swiper/swiper-bundle.min.css"
    />

    <!-- font awesome cdn link  -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>


<!-- Navbar Row 1 -->
<header class="navbar">
  <div class="navbar-row row1">
    <div class="logo">Dev<span>Notes</span></div>

    <ul class="nav-links">
      <li class="dropdown">
        <a href="#">Discover</a>
        <ul class="dropdown-menu">
          <li><a href="#">Why DevNotes</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#">Explore</a>
        <ul class="dropdown-menu">
          <li><a href="#">Note Taking</a></li>
          <li><a href="#">PDF Editor</a></li>
          <li><a href="#">PDF Converter</a></li>
          <li><a href="#">Online Notepad</a></li> 
        </ul>

      </li>
      <li class="dropdown">
        <a href="#">Plans</a>
        <ul class="dropdown-menu"> 
          <li><a href="#">Monthly</a></li>
          <li><a href="#">Annual</a></li>
        </ul>
      </li>

      <li><button class="sign-in" id="login-btn">Sign In</button></li>
    </ul>
  </div> 


 <div class="welcome-container">
  <h1>The #1 <span class="highlight">note taking</span> app for students</h1>
  <p>Organize, upload, and download CSE notes easily across 50+ courses and semesters. Start sharing knowledge today!</p>

  <!-- Only one clear 'Get Started' button -->
  <button class="cta-btn" onclick="window.location.href='signup.php'">Get Started</button>

  <div class="login-link">
    Already have an account? <a href="login.php">Log in</a>
  </div>
</div>

  </div>

 <div class="note-selector">
  <h2>NOTES</h2>
  <p>Add notes to different kinds of learning materials to work more efficiently</p>
  
  <div class="note-buttons">
    <button class="note-tab active" data-type="slides" onclick="showNote('slides')">Slides</button>
    <button class="note-tab" data-type="video" onclick="showNote('video')">Video</button>
    <button class="note-tab" data-type="textbook" onclick="showNote('textbook')">Textbook</button>
    <button class="note-tab" data-type="pdf" onclick="showNote('pdf')">PDF</button>
  </div>

  <!-- Slides -->
  <div class="note-content" id="slides" style="display: block;">
    <img src="slide-image.png" alt="Slides Content" />
  </div>

  <!-- Video -->
  <div class="note-content" id="video" style="display: none;">
    <img src="video.png" alt="Video Content"/>
  </div>

  <!-- Textbook -->
  <div class="note-content" id="textbook" style="display: none;">
    <img src="book.png" alt="Textbook Content"/>
    <p>This section could include rich text or HTML-rendered textbook content.</p>
  </div>

  <!-- PDF -->
  <div class="note-content" id="pdf" style="display: none;">
    <iframe src="your-pdf-file.pdf" width="100%" height="500px"></iframe>
  </div>
</div>

  <section class="dashboard-section">
  <div class="dashboard-card">
    <i class="fa-solid fa-file-import icon"></i>
    <h3>Upload Notes</h3>
    <p>Upload notes for any course or semester and receive a free note credit.</p>
  </div>
  <div class="dashboard-card">
    <i class="fa-solid fa-location-dot icon"></i>
    <h3>Work Anywhere</h3>
    <p>Access your notes from anywhere, they sync automatically.</p>
  </div>
  <div class="dashboard-card">
    <i class="fa-solid fa-star icon"></i>
    <h3>Rate & Review</h3>
    <p>Help others by reviewing notes and choosing the best content.</p>
  </div>
  <div class="dashboard-card">
    <i class="fa-solid fa-magnifying-glass icon"></i>
    <h3>Find Things Fast</h3>
    <p>Get the results you need, quickly and easily, with our powerful and flexible search.</p>
  </div>
</section>




<!-- footer section  -->

    <section class="footer">
      <div class="box-container">
        <div class="box">
            <h3>Need Help?</h3>
            <p>Have questions or need help?</p>
            <p>Our team is here to support your learning journey.</p>
        </div>

        <div class="box">
          <h3>Quick links</h3>
          <p><a href="#">Home</a> </p>
          <p><a href="#">Terms of Use</a></p>
          <p><a href="#">Privacy & Cookies Statement</a> </p>
          <p><a href="#">Refund Policy</a></p>
        </div>

        <div class="box">
          <h3>Follow us</h3>
          <p><a href="#">Facebook</a></p>
          <p><a href="#">Youtube</a></p>
          <p><a href="#">Twitter</a></p>
          <p><a href="#">Linkedin</a></p>
        </div>
      </div>

      <h1 class="credit">
        created by <span> DevNotes </span> | all rights reserved!
      </h1>
    </section>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>
  </body>
</html>

