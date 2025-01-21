<template>
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

export default {
  data() {
    return {
      backgroundImage,
    };
  },
};
</script>

