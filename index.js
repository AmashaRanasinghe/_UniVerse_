const testimonials = document.querySelectorAll(".testimonial");
let currentIndex = 0;

function showNextTestimonial() {
  testimonials[currentIndex].classList.remove("active");
  currentIndex = (currentIndex + 1) % testimonials.length;
  testimonials[currentIndex].classList.add("active");
}
// asaf
setInterval(showNextTestimonial, 3000);

// burger menu
const burger = document.getElementById("burger");
const navbarItems = document.getElementById("navbar-items");

burger.addEventListener("click", () => {
  navbarItems.classList.toggle("active");
});
