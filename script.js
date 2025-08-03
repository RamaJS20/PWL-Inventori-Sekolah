document.addEventListener('DOMContentLoaded', function() {
    const dropdownArrows = document.querySelectorAll('.dropdown-arrow');

    dropdownArrows.forEach(arrow => {
        arrow.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Mencegah klik pada link dari memicu event lain

            const parentItem = this.closest('.menu-item');
            const submenu = parentItem.querySelector('.submenu');

            if (submenu) {
                // Toggle display submenu
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                    parentItem.classList.remove('active');
                } else {
                    // Tutup semua submenu lain
                    document.querySelectorAll('.submenu').forEach(sub => {
                        sub.style.display = 'none';
                        sub.closest('.menu-item').classList.remove('active');
                    });
                    
                    // Buka submenu yang diklik
                    submenu.style.display = 'block';
                    parentItem.classList.add('active');
                }
            }
        });
    });

    // Menangani klik pada link di submenu
    const submenuLinks = document.querySelectorAll('.submenu a');
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation(); // Mencegah klik dari menutup submenu
        });
    });

});