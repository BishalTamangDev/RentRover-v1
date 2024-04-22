// prevent resubmission of the form
if (window.history.replaceState)
    window.history.replaceState(null, null, window.location.href);

// functions
// mistake types
// 1. integer
// 2. integerWithHyphen
// 2. integerWithPlus
// 3. float 
// 4. string

avoidMistake = (type) => {
    var ascii = event.keyCode;

    if (type == 'integer' || type == 'integerWithHyphen' || type == 'integerWithPlus' || type == 'float') {
        if (ascii == 32) {
            event.preventDefault();
        } else {
            if (ascii >= 48 && ascii <= 57) {
                // allow all
            } else {
                if (type == 'integer')
                    event.preventDefault()

                if (type == 'integerWithHyphen')
                    if (ascii != 45)
                        event.preventDefault();

                if (type == 'integerWithPlus')
                    if (ascii != 43)
                        event.preventDefault();

                if (type == 'float')
                    if (ascii != 46)
                        event.preventDefault();
            }
        }
    } else if (type == 'word') {
        if (ascii == 32) {
            event.preventDefault();
        }
    } else if (type == 'noPeriod') {
        console.log(ascii);
        if (ascii == 46)
            event.preventDefault();
    }
}

toggleCheckbox = (checkBoxId) => {
    var checkBox = document.getElementById(checkBoxId);
    checkBox.checked = !checkBox.checked;
}