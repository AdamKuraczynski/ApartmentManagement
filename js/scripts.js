function checkFileExistence(filePath, callback) {
    $.ajax({
        url: filePath,
        type: 'HEAD',
        error: function() {
            callback(false);
        },
        success: function() {
            callback(true);
        }
    });
}

function handleFileClick(event, filePath) {
    event.preventDefault();
    checkFileExistence(filePath, function(exists) {
        if (exists) {
            window.open(filePath, '_blank');
        } else {
            alert("Document no longer accessible");
        }
    });
}