function formatDate(date) {
  return new Date(date).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

function formatDateNumeric(date) {
  return new Date(date).toLocaleDateString(undefined, {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  });
};

//TODO: create component for alert
function showAlert(alert, success, message) {
    alert.show = true
    alert.type = success ? 'alert-success' : 'alert-error'
    alert.message = message

    setTimeout(() => {
        alert.show = false
    }, 2000)
}

export { formatDate, showAlert, formatDateNumeric };