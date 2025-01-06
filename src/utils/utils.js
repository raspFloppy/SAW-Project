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

function showAlert(alert, success, message) {
    alert.value = {
        show: true,
        type: success ? 'alert-success' : 'alert-error',
        message
    }

    setTimeout(() => {
        alert.value.show = false
    }, 2000)
}

export { formatDate, showAlert, formatDateNumeric };