
 document.getElementById('sidebarToggle').addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('content').classList.toggle('collapsed');
 });

        document.addEventListener('DOMContentLoaded', function () {
            const closeButton = document.querySelector('.close-btn');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            closeButton.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('collapsed');
            });
        });

        const navItems = document.querySelectorAll('.dd1');

        // Function to handle the click event
        function handleNavClick(event) {
            // Remove 'active' class from all items
            navItems.forEach(item => item.classList.remove('activenavitem'));

            // Add 'active' class to the clicked item
            event.currentTarget.classList.add('activenavitem');
        }

        // Attach click event listener to each item
        navItems.forEach(item => item.addEventListener('click', handleNavClick));



    document.addEventListener('DOMContentLoaded', () => {
        const skills = document.querySelectorAll('.skill');
        skills.forEach(skill => {
            const percentageElement = skill.querySelector('.percentage');
            const outerCircle = skill.querySelector('.outer-circle');
            const percentage = outerCircle.getAttribute('data-percentage');
            let counter = 0;

            const interval = setInterval(() => {
                if (counter === parseInt(percentage)) {
                    clearInterval(interval);
                } else {
                    counter++;
                    percentageElement.textContent = `${counter}%`;
                    const gradient = `conic-gradient(#286fac 0% ${counter}%, #ccc ${counter}% 100%)`;
                    outerCircle.style.background = gradient;
                }
            }, 20);
        });
    });

    const fileUpload = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');
    const uploadText = document.getElementById('uploadText');

    fileUpload.addEventListener('change', function() {
        const file = fileUpload.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                filePreview.src = e.target.result;
                filePreview.style.display = 'block';
                uploadText.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

