function playNext() {
  if (currentVideoIndex < chapters[currentChapterIndex].videos.length - 1) {
    currentVideoIndex++;
  } else if (currentChapterIndex < chapters.length - 1) {
    // Collapse the current chapter
    document.getElementById(`chapter${currentChapterIndex}`).style.display =
      "none";
    document.querySelector(`.chapter span`).textContent = "▼";

    currentChapterIndex++;
    currentVideoIndex = 0; // Reset to the first video of the next chapter
  } else {
    return; // No more videos to play
  }

  // Expand the new chapter
  const contentDiv = document.getElementById(`chapter${currentChapterIndex}`);
  contentDiv.style.display = "block";
  document.querySelector(`.chapter span`).textContent = "▲";

  const nextVideoDiv = contentDiv.querySelectorAll("div")[currentVideoIndex];
  const nextVideo = chapters[currentChapterIndex].videos[currentVideoIndex];

  playVideo(
    nextVideo.file,
    nextVideoDiv,
    currentChapterIndex,
    currentVideoIndex
  );
}

function playPrevious() {
  if (currentVideoIndex > 0) {
    currentVideoIndex--;
  } else if (currentChapterIndex > 0) {
    // Collapse the current chapter
    document.getElementById(`chapter${currentChapterIndex}`).style.display =
      "none";
    document.querySelector(`.chapter span`).textContent = "▼";

    currentChapterIndex--;
    currentVideoIndex = chapters[currentChapterIndex].videos.length - 1; // Go to the last video of the previous chapter
  } else {
    return; // No previous videos to play
  }

  // Expand the new chapter
  const contentDiv = document.getElementById(`chapter${currentChapterIndex}`);
  contentDiv.style.display = "block";
  document.querySelector(`.chapter span`).textContent = "▲";

  const prevVideoDiv = contentDiv.querySelectorAll("div")[currentVideoIndex];
  const prevVideo = chapters[currentChapterIndex].videos[currentVideoIndex];

  playVideo(
    prevVideo.file,
    prevVideoDiv,
    currentChapterIndex,
    currentVideoIndex
  );
}

// Add keyboard shortcuts for navigation
document.addEventListener("keydown", (event) => {
  if (event.key === "ArrowRight") {
    playNext();
  } else if (event.key === "ArrowLeft") {
    playPrevious();
  }
});

// Call fetchChapters on page load
window.onload = () => {
  const storedChapters = JSON.parse(localStorage.getItem("chapters"));

  if (storedChapters) {
    chapters = storedChapters; // ✅ Assign to global chapters variable
    generateSidebar(chapters);
  } else {
    alert("No chapter data found. Please go back and select a course.");
  }
};
