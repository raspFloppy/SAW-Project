<template>
  <ul class="menu menu-vertical lg:menu-horizontal bg-base-200 rounded-box">
    <li><a>HTML</a></li>
    <li><a>CSS</a></li>
    <li><a>JAVA SCRIPT</a></li>
    <li><a>PYTHON</a></li>
    <li><a>C</a></li>
    <li><a>C++</a></li>
    <li><a>....</a></li>
  </ul>

  <div class="hero min-h-screen" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <div class="hero-overlay bg-opacity-60"></div>
    <div class="hero-content text-neutral-content text-center">
      <div class="max-w-md">
        <h1 class="mb-5 text-5xl font-bold">Start Learning Today</h1>
        <p class="mb-5">
          Explore tutorials, courses, and resources to build your skills in programming.
        </p>
        <button class="btn btn-primary" onclick="window.location.href = './registration';">Get Started</button>
      </div>
    </div>
  </div>

  <div>
    <h1 class="text-5xl font-bold text-center">Our courses</h1>
  </div>

  <div class="container">
    <div class="card card-compact bg-base-100 w-96 shadow-xl">
      <figure>
        <img :src="htmlImage" alt="html" class="image-size" />
      </figure>
      <div class="card-body">
        <h2 class="card-title">HTML</h2>
        <p>The language for building web pages</p>
        <div class="card-actions justify-end">
          <button class="btn btn-primary">Buy Now</button>
        </div>
      </div>
    </div>

    <div class="card card-compact bg-base-100 w-96 shadow-xl">
      <figure>
        <img :src="javascriptImage" alt="java" class="image-size">
      </figure>
      <div class="card-body">
        <h2 class="card-title">JAVA SCRIPT</h2>
        <p>The language for programming web pages</p>
        <div class="card-actions justify-end">
          <button class="btn btn-primary">Buy Now</button>
        </div>
      </div>
    </div>

    <div class="card card-compact bg-base-100 w-96 shadow-xl">
      <figure>
        <img :src="cssImage" alt="css" class="image-size" />
      </figure>
      <div class="card-body">
        <h2 class="card-title">CSS</h2>
        <p>The language for styling web pages</p>
        <div class="card-actions justify-end">
          <button class="btn btn-primary">Buy Now</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer bg-neutral text-neutral-content p-10">
    <nav>
      <h6 class="footer-title">Services</h6>
      <a class="link link-hover">Coding</a>
      <a class="link link-hover">Design</a>
    </nav>
    <nav>
      <h6 class="footer-title">Company</h6>
      <a class="link link-hover">About us</a>
      <a class="link link-hover">Contact</a>
    </nav>
    <nav>
      <h6 class="footer-title">Legal</h6>
      <a class="link link-hover">Privacy policy</a>
      <a class="link link-hover">Cookie policy</a>
    </nav>
  </footer>
</template>

<script>
import backgroundImage from '@/assets/images/background.jpg';
import htmlImage from '@/assets/images/html2.png';
import javascriptImage from '@/assets/images/java.jpeg';
import cssImage from '@/assets/images/css.png';

export default {
  data() {
    return {
      backgroundImage,
      htmlImage,
      javascriptImage,
      cssImage,
    };
  },
};
</script>

<style>
.container {
  display: flex;
  gap: 16px;
  justify-content: center;
}

.image-size {
  width: 512px;
  height: 330px;
}
</style>
