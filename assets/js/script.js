let effacerVin = document.querySelectorAll(".effacer-vin");

for (let i = 0; i < effacerVin.length; i++) {
  result = effacerVin[i];
  let popup = result.parentElement.children[0];
  result.addEventListener("click", () => {
    popup.classList.add("visible");
  });
}
