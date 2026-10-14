<style>
.form-scroll {
   max-height: 700vh;          /* Height of scroll area */
    overflow-y: auto;          /* Vertical scroll only */
    overflow-x: hidden;        /* No horizontal scroll */
    padding-right: 10px;       /* Space for scrollbar */
    
    /* Firefox */
    scrollbar-width: none;
}

/* Hide scrollbar (Chrome, Edge, Safari) */
.form-scroll::-webkit-scrollbar {
    width: 0px;
    transition: 0.3s;
}

/* Show scrollbar on hover */
.form-scroll:hover::-webkit-scrollbar {
    width: 8px;
}

/* Track */
.form-scroll:hover::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

/* Thumb */
.form-scroll:hover::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #2A3F54, #4CAF50);
    border-radius: 10px;
}

/* Firefox - Show thin scrollbar on hover */
.form-scroll:hover {
    scrollbar-width: thin;
    scrollbar-color: #2A3F54 #f1f1f1;
}
</style>