function formatDate(date) {
  return new Date(date).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};


function showAlert(type, message) {
    alert.value = {
        show: true,
        type: type === 'success' ? 'alert-success' : 'alert-error',
        message
    }
    setTimeout(() => {
        alert.value.show = false
    }, 5000)
}

export { formatDate, showAlert };