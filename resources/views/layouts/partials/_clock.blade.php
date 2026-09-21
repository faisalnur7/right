<div class="draggable-clock-container hidden md:block">
    <div class="clock w-32 h-32 relative mx-auto">
        <!-- Hands -->
        <div class="hand hour" id="hour-hand"></div>
        <div class="hand minute" id="minute-hand"></div>
        <div class="hand second" id="second-hand"></div>
        <div class="center-dot"></div>
        <!-- Numbers container -->
        <div class="numbers absolute inset-0"></div>
    </div>
</div>

<style>
    .draggable-clock-container {
        position: fixed;
        top: 70px;
        right: 6px;
        cursor: grab;
        z-index: 1000;
    }

    .clock {
        border: 6px solid #000;
        border-radius: 50%;
        position: relative;
        background: #F9FAFB;
        background: #F9FAFB;
        box-shadow: inset 0px 0px 10px #222;
        width: 128px;
        height: 128px;
        opacity: .6;
        transition: .5s;
    }

    .clock:hover {
        opacity: 1;
    }


    .hand {
        position: absolute;
        top: 50%;
        left: 50%;
        transform-origin: 0% 35%;
        border-radius: 2px;
    }

    .hour {
        width: 25%;
        height: 4px;
        background: #111827;
    }

    .minute {
        width: 35%;
        height: 3px;
        background: #1F2937;
    }

    .second {
        width: 45%;
        height: 2px;
        background: #EF4444;
    }

    .center-dot {
        position: absolute;
        width: 6px;
        height: 6px;
        background: #111827;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .number {
        position: absolute;
        font-size: 1rem;
        font-weight: bold;
        color: #111827;
        transform: translate(-50%, -50%);
    }
</style>

<script>
    const clock = document.querySelector('.clock');
    const numbersContainer = document.querySelector('.numbers');
    const draggableClockContainer = document.querySelector('.draggable-clock-container');

    let isDragging = false;
    let currentX;
    let currentY;
    let initialX;
    let initialY;
    let xOffset = 0;
    let yOffset = 0;

    function createNumbers() {
        const w = clock.clientWidth;
        const h = clock.clientHeight;
        const centerX = w / 2;
        const centerY = h / 2;
        const radius = w / 2 - 10; // padding from edge
        numbersContainer.innerHTML = '';
        for (let i = 1; i <= 12; i++) {
            const angle = (i * 30 - 90) * Math.PI / 180; // angle in radians
            const x = centerX + radius * Math.cos(angle);
            const y = centerY + radius * Math.sin(angle);
            const num = document.createElement('div');
            num.className = 'number';
            num.style.left = `${x}px`;
            num.style.top = `${y}px`;
            num.textContent = i;
            numbersContainer.appendChild(num);
        }
    }

    // Update hands
    function updateClock() {
        const now = new Date();
        const seconds = now.getSeconds() + now.getMilliseconds() / 1000;
        const minutes = now.getMinutes() + seconds / 60;
        const hours = now.getHours() % 12 + minutes / 60;

        document.getElementById("second-hand").style.transform = `rotate(${(seconds/60*360)-90}deg)`;
        document.getElementById("minute-hand").style.transform = `rotate(${(minutes/60*360)-90}deg)`;
        document.getElementById("hour-hand").style.transform = `rotate(${(hours/12*360)-90}deg)`;

        requestAnimationFrame(updateClock);
    }

    // Drag functionality
    draggableClockContainer.addEventListener("mousedown", dragStart);
    draggableClockContainer.addEventListener("mouseup", dragEnd);
    draggableClockContainer.addEventListener("mousemove", drag);

    function dragStart(e) {
        initialX = e.clientX - xOffset;
        initialY = e.clientY - yOffset;

        if (e.target === draggableClockContainer || e.target.closest('.clock')) {
            isDragging = true;
            draggableClockContainer.style.cursor = 'grabbing';
        }
    }

    function dragEnd(e) {
        initialX = currentX;
        initialY = currentY;
        isDragging = false;
        draggableClockContainer.style.cursor = 'grab';
    }

    function drag(e) {
        if (isDragging) {
            e.preventDefault();
            currentX = e.clientX - initialX;
            currentY = e.clientY - initialY;

            xOffset = currentX;
            yOffset = currentY;

            setTranslate(currentX, currentY, draggableClockContainer);
        }
    }

    function setTranslate(xPos, yPos, el) {
        el.style.transform = `translate3d(${xPos}px, ${yPos}px, 0)`;
    }


    // Initialize
    createNumbers();
    updateClock();
    window.addEventListener('resize', createNumbers); // Recreate numbers on resize
</script>
