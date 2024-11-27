// Get modal and button elements
const modal = document.getElementById("createPostModal");
const btn = document.getElementById("createPostBtn");
const closeBtn = document.getElementById("closeModalBtn");

// When the user clicks the button, open the modal
btn.onclick = function () {
  modal.style.display = "block";
};

// When the user clicks on the close button, close the modal
closeBtn.onclick = function () {
  modal.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

// mentor post script
const mentorButton = document.getElementById("mentorButton");
const mentorModal = document.getElementById("mentorModal");
const closeModal = document.getElementById("closeModal");

mentorButton.addEventListener("click", () => {
  mentorModal.style.display = "flex";
});

closeModal.addEventListener("click", () => {
  mentorModal.style.display = "none";
});

window.addEventListener("click", (event) => {
  if (event.target === mentorModal) {
    mentorModal.style.display = "none";
  }
});
