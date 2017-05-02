<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner" role="listbox">
    <div class="carousel-item active">
      <!--img class="first-slide" src="" alt="First slide"-->
      <div class="container">
        <div class="carousel-caption d-none d-md-block text-left">
          <img class="first-slide" width="800px" height="450px" src="images/home.jpeg" alt="First slide">
          <p>
            We are getting with the client to fill out some cool words to put here.
          </p>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <div class="container">
        <div class="carousel-caption d-none d-md-block text-left">
          <img class="second-slide" width="800px" height="450px" src="images/dcn2.png" alt="Second slide">
            <p>
              We are also going to get better pictures once everything is 100% sexy.
            </p>
        </div>
        <!--div class="carousel-caption d-none d-md-block">
          <p><a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
        </div-->
      </div>
    </div>
    <div class="carousel-item">
      <div class="container">
        <div class="carousel-caption d-none d-md-block text-left">
          <img class="third-slide" width="800px" height="450px" src="images/dcn3.png" alt="Third slide">
          <p>
            This is our ugly golden goose.
          </p>
        </div>
      </div>

    </div>
  </div>
  <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

<script>
$('.carousel').carousel({
  interval: 5000
})
</script>
