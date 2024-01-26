document.addEventListener("DOMContentLoaded", function () {

    // Ajax-Anfrage beim Laden der Seite
    var linkToForm = document.getElementById("linktoform");
    if (linkToForm) {
        var url = linkToForm.getAttribute("href");
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Erfolgreiche Anfrage, füge den HTML-Code ins div ein
                document.getElementById("popup-registerform").innerHTML = xhr.responseText;
            }
        };
        xhr.open("GET", url, true);
        xhr.send();
    }

    // Füge ein Click-Event für das gesamte Dokument hinzu
    document.addEventListener("click", function (event) {
        var target = event.target;

        // Überprüfe, ob das geklickte Element die Klasse 'startdate-button' hat
        if (target.classList.contains('startdate-button')) {
            var startdate = target.getAttribute('data-startdate');
            document.getElementById('startdatetime-hidden').value = startdate;
            setActiveStartdate(startdate);
        }
    });
});

function setActiveStartdate(startdate) {
    var startdateButtons = document.querySelectorAll('.startdate-button');

    startdateButtons.forEach(function (button) {
        button.classList.remove('active');

        if (button.getAttribute('data-startdate') === startdate) {
            button.classList.add('active');
        }
    });
}
