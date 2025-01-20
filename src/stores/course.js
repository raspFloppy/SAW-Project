import { defineStore } from 'pinia';
import axios from 'axios';

export const useCourseStore = defineStore('course', {
  state: () => ({
    courses: [],
    currentCourse: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchCourses() {
      this.loading = true;
      try {
        const response = await axios.get('http://localhost:8000/index.php?action=get_courses', {
          withCredentials: true
        });

        console.log(response)
        if (response.data.success) {
            this.courses = response.data.courses;
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch courses';
      } finally {
        this.loading = false;
      }
    },

    async fetchCourseById(course_id) {
      this.loading = true;
      try {
        const response = await axios.get('http://localhost:8000/index.php', {
          withCredentials: true,
          params: {
            action: 'get_course',
            id: course_id
          }
        });

        console.log(response);
        if (response.data.success) {
          this.currentCourse = response.data.course;
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch course';
      } finally {
        this.loading = false;
      }
    }
  }
});

export default useCourseStore;