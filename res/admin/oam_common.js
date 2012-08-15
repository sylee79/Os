/**
 *
 */

function imageMenu(productId, imageId, imageUrl, imageDesc, isMain) {

//	calculateFinalPrice(1,'[{"price":80,"quantity":50,"quantity_to":100},{"price":85,"quantity":100,"quantity_to":""},{"price":90,"quantity":10,"quantity_to":49},{"quantity":1,"quantity_to":0,"price":100}]');
    var popup = '<div style="position: absolute; left: 300px; top: 300px; border: solid black 1px; padding: 10px; background-color: rgb(255,255,225); text-align: justify; font-size: 12px; width: 360px;">'
        + '<h2>Product Image </h2>'
        + '<div class="show_preview_photo_popup"><image src="' + imageUrl + '" width=350px></div>'
        + '<form id="updateImageDescription" action="/admin/product/updateImageDesc/" enctype="multipart/form-data" method="post">'
        + '<input type=hidden name=product_id value=' + productId + '>'
        + '<div><label>Description:</label><input type=text name=description value="' + imageDesc + '">&nbsp;<span class="yes_b" id="updateDesc">Update</span></div>'
        + '<input type=hidden name=image_id value=' + imageId + '>'
        + '</form>'
        + '<br/>'
        + '<div>'
        + '<form id="setMainPreview" action="/admin/product" enctype="multipart/form-data" method="post">'
        + '<input type=hidden name=action value=setMainPreview>'
        + '<input type=hidden name=product_id value=' + productId + '>'
        + '<input type=hidden name=image_id value=' + imageId + '>'
        + '</form>'
        + '<form id="deleteImage" action="/admin/product" enctype="multipart/form-data" method="post">'
        + '<input type=hidden name=action value=deleteImage>'
        + '<input type=hidden name=product_id value=' + productId + '>'
        + '<input type=hidden name=image_id value=' + imageId + '>'
        + '</form>';
    if (isMain == '0') {
        popup += '<span class="yes_b" id="setMain">Set This Image as Main</span>'
        + '<span class="yes_b" id="delete">Delete This Image</span>'
    }
    popup += '<span class="yes_b" id="cancel">Cancel</span>'
        + '</div>'
        + '</div>';

    $("#alertbox").html(popup);
    $("#alertbox").show();
    $("#cancel").click(function () {
        $("#alertbox").hide();
    });
    $("#setMain").click(function () {
        $("#setMainPreview").submit();
    });
    $("#updateDesc").click(function () {
        $("#updateImageDescription").submit();
    });
    $("#delete").click(function () {
        $("#deleteImage").submit();
    });
}

function calculateFinalPrice(quantity, priceListJson) {
    var priceItemList = priceListJson;
    var priceList = priceItemList.priceList;
    var totalPrice = 0;
    var leaveQuantity = quantity;

    for (var key in priceList) {
        var deductQuantity = 0;
        var price = parseFloat(priceList[key].price);
        var quantity = parseFloat(priceList[key].quantity);
        var quantity_to = parseFloat(priceList[key].quantity_to);

        if (quantity > leaveQuantity) {
            continue;
        } else if (!quantity_to) {
            deductQuantity = leaveQuantity;
        } else if ((quantity_to > leaveQuantity)) {
            deductQuantity = leaveQuantity;
        } else {
            deductQuantity = quantity_to;
        }

        totalPrice += deductQuantity * price;
        leaveQuantity -= deductQuantity;
        if (leaveQuantity < 1)
            break;
    }
    var finalAmount = (totalPrice - (totalPrice * priceItemList.customerDiscount / 100));
    return finalAmount.toFixed(2);
}
