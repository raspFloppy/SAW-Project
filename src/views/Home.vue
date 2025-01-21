<script>
import backgroundImage from '@/assets/img/background.jpeg';

export default {
    data() {
        return {
            backgroundImage,
        };
    },
};
</script>

<template>
    <div class="hero min-h-screen" :style="{ backgroundImage: `url(${backgroundImage})` }">
        <div class="hero-overlay bg-opacity-70"></div>
        <div class="hero-content text-neutral-content text-center">
            <div class="max-w-md">
                <h1 class="mb-5 text-5xl font-bold">Start Learning Today</h1>
                <p class="mb-5">
                    Explore tutorials, courses, and resources to build your skills and your future.
                </p>
                <RouterLink to="/registration" class="btn btn-primary">Get Started</RouterLink>
            </div>
        </div>
    </div>
</template>
