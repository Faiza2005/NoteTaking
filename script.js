function showNote(type) {
  const sections = document.querySelectorAll('.note-content');
  const buttons = document.querySelectorAll('.note-tab');

  sections.forEach(section => {
    section.classList.remove('active');
    section.style.display = 'none'; 
    void section.offsetWidth; 
  });
  buttons.forEach(button => button.classList.remove('active'));

  const selectedSection = document.getElementById(type);
  if (selectedSection) {
    selectedSection.style.display = 'block'; 
    setTimeout(() => selectedSection.classList.add('active'), 10); 
  }
  buttons.forEach(button => {
    if (button.dataset.type === type) {
      button.classList.add('active');
    }
  });
}
