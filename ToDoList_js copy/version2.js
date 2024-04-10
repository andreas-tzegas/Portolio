////////////////////////////////////////////// Variables//////////////////////////////////////////////
let form = document.getElementById('form');
let tache = document.getElementById('taskContent');
let taskContentName = document.getElementById('taskName');
let message = document.querySelector('span');
let ul = document.querySelector('ul');
let ajouter = document.getElementById('button')

//console.log
console.log(form)
console.log(tache)
console.log(message)


////////////////////////////////////////////// //function add tache//////////////////////////////////////////////

function inputAdd(event) {
    console.log(tache.value);

    if (tache.value.length <= 1 || (tache.value.trim() === '' || taskContentName.value.trim() === '')) {
        Message("Ajout échoué", "red");
    } else if (!isTaskUnique(tache.value)) {
        Message("La tâche existe déjà", "red");
    } else {
        Message("Ajout réussi", "green");
        tache.style.borderColor = "green";
        addItemDansList();
    }

    //  timeout ne plus afficher le mess apres 5 secondes
    setTimeout(function() {
        message.textContent = ''; 
    }, 5000); 
}
///////////////////////////////////////////////////////////// //function message////////////////////////////////////////////////////////////////////////////////////////////
function Message(messageText, color) {          
    message.textContent = messageText;                                                                  
    message.classList.remove("green", "red"); // classes rouge et vert  
    message.classList.add(color); // ajoute la couleur  
    tache.style.borderColor = color; // Changement de la couleur de la bordure de l'input
}
  ///////////////////////////////////////////////////////unique////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function isTaskUnique(taskText) {
    let lis = ul.getElementsByTagName('li');
    for (let li of lis) {
        let textNode = li.childNodes[1];
        if (textNode.textContent.trim() === taskText.trim()) {
            return false; // Tache existe deja
        }
    }
    return true; // tache est unique

}
function isTaskContentNameUnique(taskName) {
    let lis = ul.getElementsByTagName('li');
    for (let li of lis) {
        let nameSpan = li.querySelector('span:nth-child(2)'); // Select the second span element (task name)
        if (nameSpan.textContent.trim() === taskName.trim()) {
            return false; // Task content name already exists
        }
    }
    return true; // Task content name is unique
}
//////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////// function ajouter items dans la liste //////////////////////////////////////////////
function addItemDansList() {
    let li = document.createElement("li"); // ajouter element liste
    let checkbox = document.createElement("input");// variable element checkbox
    checkbox.type = "checkbox"; // Set checkbox type

    let textNode = document.createElement("span");  // type du element checkbox
    textNode.textContent = tache.value; // variable du element texteNode
    let nameNode = document.createElement("span");
    nameNode.textContent = taskContentName.value; // Set span content to task content name

    let button = document.createElement("button"); // Create delete button
    button.textContent = "Supprimer"; // Set button text content
    button.classList.add("delete_button"); // Add class to button

    // Event listener for deleting list item
    button.addEventListener('click', function() {
        li.remove();
    });

    // Event listener for checkbox change
    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            textNode.classList.add('completed'); // Add 'completed' class to text content
        } else {
            textNode.classList.remove('completed'); // Remove 'completed' class from text content
        }
    });

    // Append elements to list item
    li.appendChild(checkbox);
    li.appendChild(textNode);
    li.appendChild(nameNode);
    li.appendChild(button);

    // Append list item to the unordered list
    ul.appendChild(li);
}

//////////////////////////////////////////////// Event listener pour le submit//////////////////////////////////////////////
form.addEventListener('submit', function(event) {                
    event.preventDefault();                                                      
    inputAdd();
});

////////////////////////////////////////////////////////////////////////SCROLL/////////////////////////////////////////////////////////////////////////////////////////////////

// Function to check and enable scrolling if necessary
function checkScroll() {
    // Set the maximum number of list items before scrolling is enabled
    let maxItems = 5;

    // Get the number of list items
    let itemCount = ul.children.length;

    // Set the maximum height of the ul container
    let maxHeight = 150; // Adjust this value as needed

    // If the number of list items exceeds the maximum allowed, enable scrolling
    if (itemCount > maxItems) {
        ul.style.overflowY = 'scroll'; // Enable vertical scrolling
        ul.style.maxHeight = maxHeight + 'px'; // Set the maximum height
    } else {
        ul.style.overflowY = 'auto'; // Disable scrolling
        ul.style.maxHeight = 'none'; // Remove maximum height
    }
}

// Call the function initially and whenever a new item is added or removed
checkScroll();