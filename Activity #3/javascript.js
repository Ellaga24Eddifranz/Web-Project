
const input = document.getElementById("taskInput");
const addBtn = document.getElementById("addBtn");
const list = document.getElementById("tasklist");
const counter = document.getElementById("taskCount");

addBtn.addEventListener("click", () => {
  if (input.value.trim() === "") return;

  const li = document.createElement("li");
  li.classList.add("task");
  li.textContent = input.value;

  list.appendChild(li);

  input.value = "";
  counter.textContent = list.children.length;
});