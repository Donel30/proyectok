document.addEventListener('DOMContentLoaded', () => {
    const roomGrid = document.getElementById('room-grid');
    const formPopup = document.getElementById('form-popup');
    const clientForm = document.getElementById('client-form');
    const roomNumberInput = document.getElementById('room-number');

    // Crear habitaciones numeradas del 203 al 223
    for (let i = 203; i <= 223; i++) {
        let room = document.createElement('div');
        room.classList.add('room');
        room.innerHTML = `<img src="img\logo.jpg" alt="Habitaci�n ${i}"><br>Habitaci�n ${i}`;
        room.setAttribute('data-room', i);
        room.addEventListener('click', () => openForm(i));
        roomGrid.appendChild(room);
    }

    // Abrir el formulario
    function openForm(roomNumber) {
        // Verificar si la habitaci�n est� ocupada
        let roomElement = document.querySelector(`.room[data-room='${roomNumber}']`);
        if (!roomElement.classList.contains('occupied')) {
            formPopup.style.display = 'block';
            roomNumberInput.value = roomNumber;
        }
    }

    // Cerrar el formulario
    window.closeForm = function () {  // Asegurarte de que esta funci�n est� accesible globalmente
        formPopup.style.display = 'none';
        clientForm.reset();
    };

    // Procesar el formulario
    clientForm.addEventListener('submit', (e) => {
        e.preventDefault();

        let roomNumber = roomNumberInput.value;
        let roomElement = document.querySelector(`.room[data-room='${roomNumber}']`);

        // Marcar la habitaci�n como ocupada
        roomElement.classList.add('occupied');
        roomElement.innerHTML = `
            <img src="C:\Users\danie\OneDrive\Documentos\plaza\img\cama.png" alt="Habitaci�n ${roomNumber}">
            <br>Habitaci�n ${roomNumber} (Ocupada)
            <br><button class="desocupar" onclick="desocuparHabitacion(${roomNumber})">Desocupar</button>
        `;

        closeForm();
    });

    // Funci�n para desocupar la habitaci�n
    window.desocuparHabitacion = function (roomNumber) {
        let roomElement = document.querySelector(`.room[data-room='${roomNumber}']`);

        // Remover la clase 'occupied' y restaurar la vista original de la habitaci�n
        roomElement.classList.remove('occupied');
        roomElement.innerHTML = `
            <img src="C:\Users\danie\OneDrive\Documentos\plaza\img\cama.png" alt="Habitaci�n ${roomNumber}">
            <br>Habitaci�n ${roomNumber}
        `;

        // Volver a agregar el evento de clic para que el formulario se pueda abrir de nuevo
        roomElement.addEventListener('click', () => openForm(roomNumber));
    };
});
