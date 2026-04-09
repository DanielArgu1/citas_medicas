document.addEventListener("DOMContentLoaded", function() {
    const counters = document.querySelectorAll('.stat-value[data-count]');
    const speed = 200;

    counters.forEach(counter => {
        const target = +counter.getAttribute('data-count');
        if(target > 0){
            let count = 0;
            const increment = Math.ceil(target / speed);
            const update = () => {
                count += increment;
                if(count < target){
                    counter.innerText = count;
                    requestAnimationFrame(update);
                } else {
                    counter.innerText = target;
                }
            };
            update();
        } else {
            counter.innerText = target;
        }
    });
});


document.addEventListener('DOMContentLoaded', function(){
    const printButton = document.getElementById('admin-print-log');
    if (printButton) {
        printButton.addEventListener('click', function(){
            window.print();
        });
    }
});
