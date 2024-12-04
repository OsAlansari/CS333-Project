document.addEventListener("DOMContentLoaded", function () {
    const buildings = document.querySelectorAll(".building");
    const labSelector = document.querySelector(".lab-selector");
    const allLabs = document.querySelector(".all-labs");
    const bookingForm = document.querySelector(".booking-form");
    const form = document.querySelector("#bookingForm");
    const removeBookingBtn = document.querySelector("#removeBookingBtn");

    const labsPerBuilding = { 1: 60, 2: 60, 3: 60 };
    let selectedLab = null;

    async function fetchBookedLabs() {
        const response = await fetch("/getbooked");
        return response.json();
    }

    async function loadLabs(buildingNumber) {
        const bookedLabs = await fetchBookedLabs();
        const numLabs = labsPerBuilding[buildingNumber];
        allLabs.innerHTML = "";

        for (let i = 1; i <= numLabs; i++) {
            const lab = document.createElement("div");
            lab.classList.add("lab");

            const randomType = Math.random() < 0.5 ? "Lab" : "Class";
            lab.dataset.lab = `${randomType} ${i}`;

            lab.dataset.building = buildingNumber;
            document.body.appendChild(lab);

            const isBooked = bookedLabs.some(
                (b) => b.building == buildingNumber && b.lab_number == i
            );
            if (isBooked) {
                lab.classList.add("booked");
            } else {
                lab.addEventListener("click", function () {
                    if (!lab.classList.contains("booked")) {
                        document.querySelectorAll(".lab.selected").forEach(l =>
                            l.classList.remove("selected")
                        );
                        lab.classList.add("selected");
                        selectedLab = { building: buildingNumber, labNumber: i };
                        bookingForm.classList.remove("hidden");
                    }
                });
            }

            allLabs.appendChild(lab);
        }
        labSelector.classList.remove("hidden");
    }

    buildings.forEach(building => {
        building.addEventListener("click", function () {
            const buildingNumber = building.dataset.building;
            loadLabs(buildingNumber);
        });
    });

    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        if (selectedLab) {
            const username = form.querySelector("#username").value;
            const date = form.querySelector("#date").value;
            const time = form.querySelector("#time").value;

            const response = await fetch("/book_lab", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    name: username,
                    building: selectedLab.building,
                    lab_number: selectedLab.labNumber,
                    date: date,
                    time: time,
                }),
            });

            if (response.ok) {
                alert("Lab successfully booked!");
                bookingForm.classList.add("hidden");
                loadLabs(selectedLab.building);
                selectedLab = null;
            }
        }
    });

    removeBookingBtn.addEventListener("click", async function () {
        if (!selectedLab) {
            alert("Please select a lab to remove the booking!");
            return;
        }

        const username = form.querySelector("#username").value;
        const date = form.querySelector("#date").value;

        const bookedLabs = await fetchBookedLabs();
        const isBookedByUser = bookedLabs.some(
            (b) =>
                b.building == selectedLab.building &&
                b.lab_number == selectedLab.labNumber &&
                b.user_name == username
        );

        if (!isBookedByUser) {
            alert("This lab is not booked by you, or it's not a valid booking!");
            return;
        }

        const response = await fetch("/delete_booking", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                building: selectedLab.building,
                lab_number: selectedLab.labNumber,
                date: date,
                name: username,
            }),
        });

        if (response.ok) {
            alert("Booking removed successfully!");
            bookingForm.classList.add("hidden");
            loadLabs(selectedLab.building);
            selectedLab = null;
        } else {
            alert("Failed to remove booking. Please try again.");
        }
    });
});
