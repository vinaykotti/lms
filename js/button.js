
// Attach click event to buttons with the "open-pdf" class
document.querySelectorAll('.button').forEach(button => {
    button.addEventListener('click', (event) => {
        const pdfName = event.target.getAttribute('data-pdf');
        const videoName = event.target.getAttribute('data-video');
        const zipName = event.target.getAttribute('data-zip');

        if (pdfName) {
            // Clear existing session data
            sessionStorage.removeItem('pdf');
            sessionStorage.removeItem('video');
            sessionStorage.removeItem('zip');

            // Store new data in sessionStorage
            sessionStorage.setItem('pdf', pdfName);
            if (videoName) sessionStorage.setItem('video', videoName);
            if (zipName) sessionStorage.setItem('zip', zipName);

            // Redirect without query parameters
            window.location.href = 'viewer.php';
        } else {
            alert('No PDF file specified.');
        }
    });
});


