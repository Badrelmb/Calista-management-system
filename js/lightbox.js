// JavaScript for modal functionality
var currentPhotos = [];
var currentIndex = 0;

function openLightbox(photos) {
  currentPhotos = photos;
  currentIndex = 0;
  document.getElementById('lightbox-img').src = currentPhotos[currentIndex];
  document.getElementById('lightbox').style.display = 'flex';
}

function closeLightbox() {
  document.getElementById('lightbox').style.display = 'none';
}

function changePhoto(step) {
  currentIndex += step;
  if (currentIndex < 0) {
    currentIndex = currentPhotos.length - 1;
  } else if (currentIndex >= currentPhotos.length) {
    currentIndex = 0;
  }
  document.getElementById('lightbox-img').src = currentPhotos[currentIndex];
}
