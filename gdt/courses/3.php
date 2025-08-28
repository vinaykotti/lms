<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="gdt.css">
    <title>Sidebar Menu</title>
</head>
<body>
    <div class="sidebar" id="sidebar"></div>
    <div class="video-container">
        <h2>Video Player</h2>
        <video id="videoPlayer" controls>
            <source src="" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
	
	<div class="navigation-buttons">
    <button id="prevButton" onclick="playPrevious()">Previous</button>
    <button id="nextButton" onclick="playNext()">Next</button>
	</div>

    <script>
let lastActiveVideo = null; // Variable to keep track of the last active video
let currentExpandedChapter = null; // Track the currently expanded chapter
let currentChapterIndex = 0; // Initialize current chapter index
let currentVideoIndex = 0; // Initialize current video index
let chapters = []; // Initialize as an empty array

async function fetchChapters() {
    try {
        const response = await fetch('get_chapters.php'); // Fetch data from the backend
        chapters = await response.json(); // Assign the fetched data to the global chapters variable
        generateSidebar(chapters);
    } catch (error) {
        console.error('Error fetching chapters:', error);
    }
}

function generateSidebar(chapters) {
    const sidebar = document.getElementById("sidebar");
    sidebar.innerHTML = ''; // Clear existing content

    // Add the heading to the sidebar
    const heading = document.createElement("h2");
    heading.textContent = "Fundamentals of GD&T ASME Y14.5-2018";
    heading.classList.add("sidebar-heading"); // Optional: Add a class for styling
    sidebar.appendChild(heading);

    chapters.forEach((chapter, index) => {
        // Create chapter header
        const chapterDiv = document.createElement("div");
        chapterDiv.classList.add("chapter");
        chapterDiv.innerHTML = `${chapter.title} <span>▼</span>`;
        chapterDiv.onclick = () => toggleContent(`chapter${index}`, chapterDiv, index);

        // Create chapter content
        const contentDiv = document.createElement("div");
        contentDiv.id = `chapter${index}`;
        contentDiv.classList.add("content");

        chapter.videos.forEach((video, videoIndex) => {
            const videoDiv = document.createElement("div");
            videoDiv.innerHTML = `<span class="icon">▶️</span> ${video.title}`;
            videoDiv.onclick = () => playVideo(video.file, videoDiv, index, videoIndex); // Pass the videoDiv to highlight it
            contentDiv.appendChild(videoDiv);
        });

        sidebar.appendChild(chapterDiv);
        sidebar.appendChild(contentDiv);
    });
}

function toggleContent(id, chapterDiv, chapterIndex) {
    const content = document.getElementById(id);

    // Collapse the currently expanded chapter if it's not the one being clicked
    if (currentExpandedChapter && currentExpandedChapter !== id) {
        const previousContent = document.getElementById(currentExpandedChapter);
        if (previousContent) {
            previousContent.style.display = 'none'; // Collapse the previous chapter
            const previousChapterDiv = document.querySelector(`.chapter span[data-index="${currentExpandedChapter}"]`);
            if (previousChapterDiv) {
                previousChapterDiv.textContent = '▼'; // Reset arrow
            }
        }
        currentExpandedChapter = null; // Reset the expanded chapter
    }

    // Toggle the clicked chapter
    if (content.style.display === "block") {
        content.style.display = "none"; // Collapse
        chapterDiv.querySelector("span").textContent = "▼"; // Reset arrow
    } else {
        content.style.display = "block"; // Expand
        chapterDiv.querySelector("span").textContent = "▲"; // Change arrow to indicate expanded
        currentExpandedChapter = id; // Update the expanded chapter
    }
}

function playVideo(videoSrc, videoDiv, chapterIndex, videoIndex) {
    const videoPlayer = document.getElementById("videoPlayer");

    // Prepend the base path to the video source
    const basePath = 'Fundamentals of GD&T ASME Y14.5-2018/'; // Adjust this if your videos folder is in a different location
    videoPlayer.src = basePath + videoSrc;

    videoPlayer.play();

    // Highlight the active video
    if (lastActiveVideo) {
        lastActiveVideo.classList.remove("active-video");
    }
    videoDiv.classList.add("active-video");
    lastActiveVideo = videoDiv;

    // Update current chapter and video indices
    currentChapterIndex = chapterIndex;
    currentVideoIndex = videoIndex;

    // Save playback state to localStorage
    localStorage.setItem("currentChapterIndex", currentChapterIndex);
    localStorage.setItem("currentVideoIndex", currentVideoIndex);

    // Collapse the previous chapter and expand the current one
    if (currentExpandedChapter && currentExpandedChapter !== `chapter${chapterIndex}`) {
        document.getElementById(currentExpandedChapter).style.display = 'none';
        document.querySelector(`.chapter span[data-index="${currentExpandedChapter}"]`).textContent = '▼';
    }
    currentExpandedChapter = `chapter${chapterIndex}`;
    document.getElementById(currentExpandedChapter).style.display = 'block';
    document.querySelector(`.chapter span[data-index="${currentExpandedChapter}"]`).textContent = '▲';

    // Auto-scroll to the active video
    videoDiv.scrollIntoView({ behavior: "smooth", block: "center" });
}

 // error on video load
 videoPlayer.onerror = function() {
            alert("Error loading video. Please try again later.");
        };


    </script>
 <script src="javascript.js" type="text/javascript"></script>
</body>
</html>

