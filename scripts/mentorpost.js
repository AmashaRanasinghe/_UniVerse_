// Get elements
const mentorButton = document.getElementById("mentorButton");
const mentorModal = document.getElementById("mentorModal");
const closeModal = document.getElementById("closeModal");

// Show the modal when the button is clicked
mentorButton.addEventListener("click", () => {
    mentorModal.style.display = "flex"; // Make modal visible
});

// Close the modal when the close button is clicked
closeModal.addEventListener("click", () => {
    mentorModal.style.display = "none"; // Hide modal
});

// Close the modal when clicking outside the modal content
window.addEventListener("click", (event) => {
    if (event.target === mentorModal) {
        mentorModal.style.display = "none"; // Hide modal
    }
});
