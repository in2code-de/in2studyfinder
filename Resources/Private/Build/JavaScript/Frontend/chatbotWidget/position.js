// Position Component for MistralChatbot
export class Position {
    constructor(chatbotWidget, storage) {
        this.chatbotWidget = chatbotWidget;
        this.storage = storage;
    }

    setupDraggable() {
        const header = this.chatbotWidget.querySelector('.chatbot-header');
        const self = this;

        let isDragging = false;
        let offsetX, offsetY;

        // Store initial position to use as reference
        let initialX, initialY;

        // Function to handle start of drag
        const startDrag = (e) => {
            // Get clientX and clientY based on event type (mouse or touch)
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;

            // Prevent dragging only when clicking specific interactive elements
            if (e.target.getAttribute('data-chatbot-toggle') !== null ||
                e.target.getAttribute('data-chatbot-input') !== null ||
                e.target.getAttribute('data-chatbot-send') !== null) return;

            // Prevent default behavior to avoid text selection during drag start
            e.preventDefault();

            isDragging = true;

            // Add no-select class to body to prevent text selection during dragging
            document.body.classList.add('no-select');

            // Get the current position of the chatbot
            const rect = this.chatbotWidget.getBoundingClientRect();

            // Calculate the offset from the mouse/touch position to the chatbot corner
            offsetX = clientX - rect.left;
            offsetY = clientY - rect.top;

            // Store initial position
            initialX = rect.left;
            initialY = rect.top;

            // Change cursor to indicate dragging
            if (e.target.closest('.chatbot-header')) {
                header.style.cursor = 'grabbing';
            }
            this.chatbotWidget.style.cursor = 'grabbing';
        };

        // Function to handle drag movement
        const drag = (e) => {
            if (!isDragging) return;

            // Prevent default behavior to avoid text selection during drag
            e.preventDefault();

            // Get clientX and clientY based on event type (mouse or touch)
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;

            // Calculate new position
            let newX = clientX - offsetX;
            let newY = clientY - offsetY;

            // Get chatbot dimensions
            const chatbotWidth = this.chatbotWidget.offsetWidth;
            const chatbotHeight = this.chatbotWidget.offsetHeight;

            // Ensure the chatbot stays within the viewport
            // Keep at least 50px of the chatbot visible on each edge
            const minVisiblePart = 50;

            // Check right edge
            if (newX + chatbotWidth < minVisiblePart) {
                newX = minVisiblePart - chatbotWidth;
            }

            // Check bottom edge
            if (newY + chatbotHeight < minVisiblePart) {
                newY = minVisiblePart - chatbotHeight;
            }

            // Check left edge
            if (newX > window.innerWidth - minVisiblePart) {
                newX = window.innerWidth - minVisiblePart;
            }

            // Check top edge
            if (newY > window.innerHeight - minVisiblePart) {
                newY = window.innerHeight - minVisiblePart;
            }

            // Update position using transform for better performance
            this.chatbotWidget.style.transform = `translate(${newX - initialX}px, ${newY - initialY}px)`;
        };

        // Function to handle end of drag
        const endDrag = () => {
            if (!isDragging) return;

            isDragging = false;

            // Remove no-select class from body to re-enable text selection
            document.body.classList.remove('no-select');

            // Reset cursor
            header.style.cursor = 'grab';
            this.chatbotWidget.style.cursor = '';

            // Get the final position after dragging
            const rect = this.chatbotWidget.getBoundingClientRect();

            // Calculate the distance from the edges of the viewport
            const right = window.innerWidth - rect.right;
            const bottom = window.innerHeight - rect.bottom;

            // Update the chatbot's position using CSS properties
            this.chatbotWidget.style.right = right + 'px';
            this.chatbotWidget.style.bottom = bottom + 'px';
            this.chatbotWidget.style.left = 'auto';
            this.chatbotWidget.style.top = 'auto';

            // Reset transform
            this.chatbotWidget.style.transform = 'none';

            // Save the position for future page loads
            self.savePosition(right, bottom);
        };

        // Mouse events
        this.chatbotWidget.addEventListener('mousedown', startDrag);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', endDrag);

        // Touch events for mobile support
        this.chatbotWidget.addEventListener('touchstart', startDrag);
        document.addEventListener('touchmove', drag, { passive: false });
        document.addEventListener('touchend', endDrag);
    }

    savePosition(right, bottom) {
        // Save the position to localStorage using the storage utility
        this.storage.savePosition(right, bottom);
    }

    loadPosition() {
        // Check if we're on a mobile device
        const isMobile = window.innerWidth <= 480;

        // Try to load the position from localStorage
        const position = this.storage.loadPosition();

        if (position && !isMobile) {
            // Apply the saved position
            if (position.right !== undefined && position.bottom !== undefined) {
                this.chatbotWidget.style.right = position.right + 'px';
                this.chatbotWidget.style.bottom = position.bottom + 'px';
            }
        } else if (isMobile) {
            // Reset position for mobile devices to ensure visibility
            this.chatbotWidget.style.right = '20px';
            this.chatbotWidget.style.bottom = '20px';
            this.chatbotWidget.style.transform = 'none';
        }
    }
}
