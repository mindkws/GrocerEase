// Menu switching functionality
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        // Remove active class from all menu items
        document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
        
        // Add active class to clicked item
        this.classList.add('active');

        // Hide all content sections
        document.querySelector('.product-list').classList.remove('active');
        document.querySelector('.promotion-list').classList.remove('active');

        // Show selected content section
        const section = this.getAttribute('data-section');
        document.querySelector(`.${section}-list`).classList.add('active');
    });
});