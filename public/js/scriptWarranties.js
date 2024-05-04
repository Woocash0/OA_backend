const exit_button = document.querySelector('.exit');
const background = document.querySelector('.notification-overlay');
const box = document.querySelector('.notification-panel');
const header = document.querySelector('header');
const ElipseContainer = document.querySelector('#elipse');

const detailCategory = document.querySelector('#detail_category');
const detailProductName = document.querySelector('#detail_product_name');
const detailPurchaseDate = document.querySelector('#detail_purchase_date');
const detailWarrantyPeriod = document.querySelector('#detail_warranty_period');
const detailReceipt = document.querySelector('#detail_receipt');
const detailTags = document.querySelector('#detail_tags');

///////////////////////////////////////////////////////////////////////////////////////////
// Show/Hide Notification Panel
function attachEventListenersToWarrantyBoxes() {
    const warrantyBoxes = document.querySelectorAll('.warranty_box');

    warrantyBoxes.forEach(function (warrantyBox) {
        warrantyBox.removeEventListener('click', handleClick);
        warrantyBox.addEventListener('click', handleClick);
    });

    const locationDiv = document.querySelector(".location");
    locationDiv.removeEventListener("click", handleLocationClick);
    locationDiv.addEventListener("click", handleLocationClick);
}

let isDetailReceiptVisible = false;
let warrantyBox;

function handleClick() {
    showDetails.call(this, background, box);
    deleteById.call(this);
    editById.call(this);

}

function showDetails(element1, element2) {
    warrantyBox = this;

    const content_Category = warrantyBox.querySelector('.imgname').textContent;
    const content_productName = warrantyBox.querySelector('#productName').textContent;
    const content_PurchaseDate = warrantyBox.querySelector('#purchaseDate').textContent;
    const content_WarrantyPeriod = warrantyBox.querySelector('#warrantyPeriod').textContent;
    const content_Tags = warrantyBox.querySelector('#tags').textContent;

    detailCategory.innerHTML = content_Category;
    detailProductName.innerHTML = content_productName;
    detailPurchaseDate.innerHTML = content_PurchaseDate;
    detailTags.innerHTML = content_Tags;

    let year = "lat";
    if (content_WarrantyPeriod === "1") {
        year = 'rok';
    } else if (parseInt(content_WarrantyPeriod) > 1 && parseInt(content_WarrantyPeriod) < 5) {
        year = 'lata';
    } else {
        year = 'lat';
    }

    detailWarrantyPeriod.innerHTML = content_WarrantyPeriod + " " + year;

    purchaseDate_v = content_PurchaseDate;
    warrantyPeriod_v = parseInt(content_WarrantyPeriod);
    updateRemainingTime(purchaseDate_v, warrantyPeriod_v);
    setInterval(function () {
        updateRemainingTime(purchaseDate_v, warrantyPeriod_v);
    }, 1000);

    element1.classList.add('details_background');
    element2.classList.add('details_box');
}

function handleLocationClick() {
    const currentContent_Receipt = warrantyBox.querySelector('#receipt').textContent;

    if (isDetailReceiptVisible) {
        detailReceipt.style.display = 'none';
        detailReceipt.innerHTML = "";
        ElipseContainer.style.border = '15px solid #261132';
        isDetailReceiptVisible = false;
    } else {
        detailReceipt.style.display = 'flex';
        const existingImage = detailReceipt.querySelector("img");
        if (!existingImage) {
            const image = document.createElement("img");
            image.src = `./uploads/${currentContent_Receipt}`;
            detailReceipt.appendChild(image);
            ElipseContainer.style.border = 'none';
            isDetailReceiptVisible = true;
        }
    }
}

attachEventListenersToWarrantyBoxes();

function hideDetails(element1, element2) {
    element1.classList.remove('details_background');
    element2.classList.remove('details_box');

    if (isDetailReceiptVisible) {
        detailReceipt.style.display = 'none';
        detailReceipt.innerHTML = "";
        ElipseContainer.style.border = '15px solid #261132';
        isDetailReceiptVisible = false;
    }
}
background.addEventListener('click', function() {
    hideDetails(background, box);
});
exit_button.addEventListener('click', function() {
    hideDetails(background, box);
});

///////////////////////////////////////////////////////////////////////////////////////////
// Deleting warranties
function deleteById() {
    const warrantyBox = this;
    const deleteElementId = warrantyBox.id;

    const deleteIcon = document.querySelector(".delete");
    deleteIcon.addEventListener("click", function () {
        window.location.href = 'delete_warranty/'+deleteElementId;
    });
}

///////////////////////////////////////////////////////////////////////////////////////////
// Editing warranties
function editById() {
    const warrantyBox = this;
    const editElementId = warrantyBox.id;

    const editIcon = document.querySelector(".edit");
    editIcon.addEventListener("click", function () {
        window.location.href = 'edit_warranty/'+editElementId;
    });
}

///////////////////////////////////////////////////////////////////////////////////////////
// Show/Hide Search Button
const searchBlock = document.getElementById('search_block');
const searchButton = document.getElementById('search_button');
const textInput = document.getElementById('searchbar');

searchBlock.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default behavior of the anchor tag
    searchButton.style.display = 'none';
    textInput.style.display = 'inline-block';
    textInput.focus();
});

textInput.addEventListener('blur', function() {
    if (window.innerWidth > 992) {
        searchButton.style.display = 'inline-block';
    }
    textInput.style.display = 'none';

});

///////////////////////////////////////////////////////////////////////////////////////////
// Searching warranties
const search = textInput;
const warrantyContainer = document.querySelector(".warranties");

search.addEventListener("keyup", function (event){
    if (event.key === "Enter")
    {
        event.preventDefault();

        const data = {search: this.value};

        fetch("/search", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(function (response) {
            return response.json();
        }).then(function (warranties){
            warrantyContainer.innerHTML = "";
            loadWarranties(warranties)
        });
    }

});

function loadWarranties(warranties){
    warranties.forEach(warranty =>{
        createWarranty(warranty);
    });

    attachEventListenersToWarrantyBoxes();
}

 function createWarranty(warranty){
    const template = document.querySelector("#warranty-template");
    const clone = template.content.cloneNode(true);

    header.innerHTML = 'Search result:';
    const warrantyBoxId = clone.querySelector('.warranty_box');
    warrantyBoxId.id = warranty.id;
    const category = clone.querySelector(".imgname");
    category.innerHTML = warranty.category;
    const image = clone.querySelector("img");
    image.src = `./img/${warranty.category}.svg`;

    const productName = clone.querySelector("#productName");
    productName.innerHTML = warranty.productName;
    const purchaseDate = clone.querySelector("#purchaseDate");
    
    const dateTime = new Date(warranty.purchaseDate);
    const year = dateTime.getFullYear();
    const month = String(dateTime.getMonth() + 1).padStart(2, '0');
    const day = String(dateTime.getDate()).padStart(2, '0');
    const formattedDate = year + "-" + month + "-" + day;


    purchaseDate.innerHTML = formattedDate;
    const warrantyPeriod = clone.querySelector("#warrantyPeriod");
    warrantyPeriod.innerHTML = warranty.warrantyPeriod;
    const receipt = clone.querySelector("#receipt");
    receipt.innerHTML = warranty.receipt;
    const tags = clone.querySelector("#tags");
    var tagList = "";
    var promises = [];

    for (var i = 0; i < warranty.tags.length; i++) {
        var url = "http://127.0.0.1:8000" + warranty.tags[i];

        promises.push(
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const tagName = data.name;
                    return tagName;
                })
                .catch(error => console.error('Błąd:', error))
        );
    }

    Promise.all(promises)
        .then(tagNames => {

            tagList = tagNames.join(', '); 
            tags.innerHTML = tagList;
        })
        .catch(error => console.error('Błąd przy pobieraniu danych:', error));

    warrantyContainer.appendChild(clone);
}

///////////////////////////////////////////////////////////////////////////////////////////
// clickable icons
document.getElementById("li-warranties").addEventListener("click", function() {
    window.location.href = "warranties";
});
document.getElementById("li-archive").addEventListener("click", function() {
    window.location.href = "archive";
});
document.getElementById("li-account").addEventListener("click", function() {
    window.location.href = "account";
});


///////////////////////////////////////////////////////////////////////////////////////////
// Calculate remaining warranty time
const timeElements = document.querySelectorAll("#elipse .timebox b");

function updateRemainingTime(purchaseDate_v, warrantyYears) {
    const purchaseDate = new Date(purchaseDate_v);
    const currentDate = new Date();

    const warrantyEndDate = new Date(
        purchaseDate.getFullYear() + warrantyYears,
        purchaseDate.getMonth(),
        purchaseDate.getDate()
    );

    const remainingTime = warrantyEndDate.getTime() - currentDate.getTime();

    const remainingDays = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
    const remainingHours = Math.floor((remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const remainingMinutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
    const remainingSeconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

    timeElements[0].textContent = remainingDays;
    timeElements[1].textContent = remainingHours;
    timeElements[2].textContent = remainingMinutes;
    timeElements[3].textContent = remainingSeconds;
}



