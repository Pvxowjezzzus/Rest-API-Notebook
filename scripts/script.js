const form = document.querySelector('#contactForm');
const messages = new Map([
    ['fio', 'Поле "ФИО" заполнено неправильно'],
    ['company', 'Поле "Компания" заполнено неправильно'],
    ['phone', 'Поле "Телефон" заполнено неправильно'],
    ['email', 'Поле "Email" заполнено неправильно'],
    ['photo', 'Запрещенный тип файла'],
]);

form.addEventListener('submit', function(e) {
    e.preventDefault()
    let formData = new FormData(this);
    let xhr = new XMLHttpRequest();
    let url = form.action;
    xhr.open('POST', url, true);
    xhr.send(formData);
    xhr.onload = function() {
        let data = JSON.parse(xhr.responseText);
        if (xhr.readyState === 4) {
            const errors = document.querySelectorAll('.error-msg')
            errors.forEach(function(elem) {
                elem.parentNode.removeChild(elem);
            })
            if (xhr.status === 400) {
                for (let str of data['object']) {
                    const msg = messages.get(str);
                    const input = document.querySelector(`#${str}`);
                    const p = document.createElement("p");
                    p.classList.add('error-msg');
                    p.innerHTML = msg;
                    const textBlock = input.previousSibling.previousSibling;
                    textBlock.querySelector('.error-msg') === null ? textBlock.appendChild(p) :
                        console.log(msg);
                };
            }
            if (xhr.status === 200) {
                alert(data['message']);
                form.reset();
                if (data['message'] === 'Данные записи изменены!') {
                    window.location.href = `/note.php?id=${data['object']}`;
                }
            }
        }
    }
})