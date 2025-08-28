

let pdfDoc = null;
let scale = 1.5;
const pdfContainer = document.getElementById('pdf-container');
const bookmarksContainer = document.getElementById('bookmarks');
const zoomInButton = document.getElementById('zoom-in');
const zoomOutButton = document.getElementById('zoom-out');
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-btn');
const prevButton = document.getElementById('prev-btn');
const nextButton = document.getElementById('next-btn');


// Get the PDF name from the query parameter
const urlParams = new URLSearchParams(window.location.search);
const pdfName = sessionStorage.getItem('pdf');

if (!pdfName) {
    alert("No PDF specified. Returning to the home page.");
    window.location.href = "courses.html";
}

// Function to render a single page
const renderPage = (pageNum) => {
    pdfDoc.getPage(pageNum).then((page) => {
        const viewport = page.getViewport({ scale: scale });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        canvas.width = viewport.width;
        canvas.height = viewport.height;

        page.render({
            canvasContext: context,
            viewport: viewport,
        });

        canvas.classList.add('pdf-page');
        canvas.dataset.pageNumber = pageNum;
        pdfContainer.appendChild(canvas);
    });
};

// Render all pages
const renderAllPages = () => {
    pdfContainer.innerHTML = ''; // Clear container
    for (let i = 1; i <= pdfDoc.numPages; i++) {
        renderPage(i);
    }
};

// Zoom in/out functionality
const zoomIn = () => {
    scale += 0.2;
    renderAllPages();
};

const zoomOut = () => {
    if (scale > 0.4) {
        scale -= 0.2;
        renderAllPages();
    }
};

document.getElementById('zoom-in').addEventListener('click', zoomIn);
document.getElementById('zoom-out').addEventListener('click', zoomOut);

// Load the PDF document
const loadPDF = (pdfName) => {
    const pdfURL = `pdfs/${pdfName}`;
    pdfjsLib.getDocument(pdfURL).promise
        .then((doc) => {
            pdfDoc = doc;
            renderAllPages();
        })
        .catch((error) => {
            console.error('Error loading PDF:', error);
            alert('Failed to load PDF. Returning to the home page.');
            window.location.href = "courses.html";
        });
};





// Event listeners
document.querySelector('#zoom-in').addEventListener('click', zoomIn);
document.querySelector('#zoom-out').addEventListener('click', zoomOut);
document.querySelector('#search-btn').addEventListener('click', () => {
    const searchTerm = document.querySelector('#search-input').value;
    searchText(searchTerm);
});











// Load and render bookmarks
const loadBookmarks = async () => {
    const outline = await pdfDoc.getOutline();
    bookmarksContainer.innerHTML = ''; // Clear container
    if (outline) {
        renderBookmarks(outline, bookmarksContainer);
    } else {
        bookmarksContainer.innerHTML = '<p>No bookmarks found.</p>';
    }
};

// Render bookmarks recursively
const renderBookmarks = (bookmarks, container) => {
    bookmarks.forEach((bookmark) => {
        const bookmarkElement = document.createElement('div');
        bookmarkElement.classList.add('bookmark');
        bookmarkElement.textContent = bookmark.title;

        bookmarkElement.addEventListener('click', async () => {
            searchAndNavigateToText(bookmark.title);
        });

        container.appendChild(bookmarkElement);

        if (bookmark.items && bookmark.items.length > 0) {
            const subContainer = document.createElement('div');
            subContainer.style.paddingLeft = '20px';
            renderBookmarks(bookmark.items, subContainer);
            container.appendChild(subContainer);
        }
    });
};

// Search for text in the PDF and navigate to the first occurrence
const searchAndNavigateToText = async (text) => {
    if (!pdfDoc) {
        alert("No PDF loaded. Please check the data-pdf attribute.");
        return;
    }

    for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
        const page = await pdfDoc.getPage(pageNum);
        const textContent = await page.getTextContent();

        // Check if the text matches on this page
        const found = textContent.items.some((item) => item.str.includes(text));
        if (found) {
            const matchingCanvas = document.querySelector(`[data-page-number="${pageNum}"]`);
            if (matchingCanvas) {
                pdfContainer.scrollTo({ top: matchingCanvas.offsetTop, behavior: 'smooth' });
            }
            return; // Exit after finding the first occurrence
        }
    }

    alert(`Text "${text}" not found in the document.`);
};


// Load the PDF document
const loadbookmarkPDF = (pdfName) => {
    const pdfURL = `pdfs/${pdfName}`; // Construct URL
    pdfjsLib.getDocument(pdfURL).promise
        .then((doc) => {
            pdfDoc = doc;
            renderAllPages();
            loadBookmarks();
        })
        .catch((error) => {
            console.error('Error loading PDF:', error);
            alert('Failed to load PDF. Please try again.');
        });
};

// Load the specified PDF
loadbookmarkPDF(pdfName);





// video code starts here //


document.addEventListener("DOMContentLoaded", () => {

    const videoFolder = sessionStorage.getItem('video');


    const dropdown = document.getElementById('fileDropdown');
    const videoModal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('videoPlayer');
    const overlay = document.getElementById('overlay');
    const closeModal = document.getElementById('closeModal');

    if (videoFolder) {
        loadVideos(videoFolder);
    }

    // Show dropdown and fetch video list
    document.getElementById('showDropdownBtn').addEventListener('click', () => {
        const videoFolder = sessionStorage.getItem('video'); // Get the folder name from the URL

        if (!videoFolder) {
            alert("No video folder specified in the URL!");
            return;
        }

        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
            loadVideos(videoFolder); // Fetch videos from the specified folder
        } else {
            dropdown.style.display = 'none';
        }
    });


    // Fetch video files from the specified folder
    function loadVideos(folder) {
        fetch(`filenames.php?video=${encodeURIComponent(folder)}`)
            .then(response => response.json())
            .then(files => {
                dropdown.innerHTML = '<option value="">-- Select Video --</option>'; // Reset dropdown
                files.forEach(file => {
                    const option = document.createElement('option');
                    option.value = file;
                    option.textContent = file;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching video list:', error));
    }

    // Show popup video player on file selection
    dropdown.addEventListener('change', () => {
        const selectedFile = dropdown.value;
        if (selectedFile && videoFolder) {
            videoPlayer.src = `videofetcher.php?video=${encodeURIComponent(videoFolder)}&file=${encodeURIComponent(selectedFile)}`;
            videoModal.style.display = 'block';
            overlay.style.display = 'block';

            // Disable right-click on the video element
            videoPlayer.oncontextmenu = (e) => {
                e.preventDefault();
                alert('Video download is disabled.');
            };
        }
    });

    // Close modal
    closeModal.addEventListener('click', () => {
        videoModal.style.display = 'none';
        overlay.style.display = 'none';
        videoPlayer.pause();
    });
});




// Search functionality
const searchDocument = (query) => {
    searchResults = [];
    currentSearchIndex = -1;

    const searchPages = async () => {
        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            const page = await pdfDoc.getPage(pageNum);
            const textContent = await page.getTextContent();

            // Check for matches on the page
            const pageText = textContent.items.map(item => item.str).join(' ').toLowerCase();
            let matchIndex = pageText.indexOf(query);
            while (matchIndex !== -1) {
                searchResults.push({ pageNum });
                matchIndex = pageText.indexOf(query, matchIndex + query.length);
            }
        }

        if (searchResults.length > 0) {
            alert(`Found ${searchResults.length} match(es).`);
            currentSearchIndex = 0;
            navigateToResult(currentSearchIndex);
            updateButtons();
        } else {
            alert("No matches found.");
            updateButtons();
        }
    };

    searchPages();
};

// Navigate to a specific search result
const navigateToResult = (index) => {
    if (index >= 0 && index < searchResults.length) {
        const { pageNum } = searchResults[index];
        const matchingCanvas = document.querySelector(`[data-page-number="${pageNum}"]`);
        if (matchingCanvas) {
            const topOffset = matchingCanvas.offsetTop;
            pdfContainer.scrollTo({ top: topOffset, behavior: 'smooth' });
        }
    }
};

// Update the state of Next/Previous buttons
const updateButtons = () => {
    prevButton.disabled = currentSearchIndex <= 0;
    nextButton.disabled = currentSearchIndex >= searchResults.length - 1;
};

// Handle Search button click
searchButton.addEventListener('click', () => {
    const query = searchInput.value.trim().toLowerCase();
    if (!query) {
        alert("Please enter a search term.");
        return;
    }
    searchDocument(query);
});

// Handle Next button click
nextButton.addEventListener('click', () => {
    if (currentSearchIndex < searchResults.length - 1) {
        currentSearchIndex++;
        navigateToResult(currentSearchIndex);
        updateButtons();
    }
});

// Handle Previous button click
prevButton.addEventListener('click', () => {
    if (currentSearchIndex > 0) {
        currentSearchIndex--;
        navigateToResult(currentSearchIndex);
        updateButtons();
    }
});



//code to download ZIP files //

function downloadzip() {

    // Get the URL parameters
    const urlParams = new URLSearchParams(window.location.search);

    // Extract the 'zip' parameter from the URL
    const zipFolderName = sessionStorage.getItem('zip');

    // Check if the 'zip' parameter exists
    if (zipFolderName) {
        const zipLink = document.getElementById('zipLink');
        // Dynamically update the href with the zip folder name from the URL
        zipLink.href = `workingfiles/${zipFolderName}.zip`; // Assuming the ZIP files are stored in 'workingfiles' folder
    } else {
        console.log('No zip folder specified in the URL.');
    }
}


// Disable right-click
document.addEventListener('contextmenu', (event) => event.preventDefault());

// Disable print dialog
document.addEventListener('keydown', (e) => {
    // Block Ctrl+P or Cmd+P (for Mac) to prevent print
    if ((e.ctrlKey && e.key === 'p') || (e.metaKey && e.key === 'p')) {
        e.preventDefault();
        alert('Printing is disabled on this page.');
    }
});

// Disable Ctrl + Scroll (Zoom)
document.addEventListener('wheel', (e) => {
    // Check if Ctrl key is pressed and wheel is scrolled
    if (e.ctrlKey) {
        e.preventDefault(); // Prevent the default zoom behavior
        alert('Zooming with Ctrl + Scroll is disabled.');
    }
}, { passive: false });





setInterval(() => {
    const extensionElements = document.querySelectorAll('[id^="ext-"], [class^="ext-"]');
    if (extensionElements.length > 0) {
        alert("Browser extensions are not allowed. Please disable them.");
        window.location.href = "about:blank";
    }
}, 3000);


function detectExtensions() {
    let detected = false;

    // Check for common extension indicators
    const extensionIds = [
        'chrome-extension', 'moz-extension', 'ms-browser-extension'
    ];

    extensionIds.forEach(id => {
        if (document.documentElement.innerHTML.includes(id)) {
            detected = true;
        }
    });

    if (detected) {
        alert("Extensions are not allowed on this website. Please disable them.");
        window.location.href = "about:blank"; // Redirect user
    }
}

// Run detection after page load
window.onload = detectExtensions;
window.onload = setInterval;


// Function to detect a specific extension
function detectSpecificExtension() {
    // Example: Check for a hypothetical global variable added by an extension
    if (typeof window.someExtensionGlobalVariable !== 'undefined') {
        return true;
    }

    // Add more checks for other known changes made by the extension
    return false;
}

// Check for the extension on page load
window.onload = function () {
    if (detectSpecificExtension()) {
        alert('A specific extension is active. Please disable it to continue using this site.');
    }
};