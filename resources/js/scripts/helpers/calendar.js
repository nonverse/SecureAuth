// Calendar {Day, Month, Year, etc...}
class calendar {
    constructor() {
        this.current = new Date();
        this.days = {
            0: "Sunday",
            1: "Monday",
            2: "Tuesday",
            3: "Wednesday",
            4: "Thursday",
            5: "Friday",
            6: "Saturday",
        };
        this.months = {
            0: "January",
            1: "February",
            2: "March",
            3: "April",
            4: "May",
            5: "June",
            6: "July",
            7: "August",
            8: "September",
            9: "October",
            10: "November",
            11: "December",
        };
    }

    date() {
        return this.current.getDate()
    }

    formatDate(date, seperator) {
        const dateArray = date.split(seperator)

        return `${this.months[dateArray[1]-1]} ${dateArray[2]}, ${dateArray[0]}`
    }
}

export default new calendar()