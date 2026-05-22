function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.style.display = 'flex';
    }
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.style.display = 'none';
    }
}

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
});
