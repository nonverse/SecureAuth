class validate {

    email(value) {
        let error;
        if (!value) {
            error = "An email is required"
        } else if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value)) {
            error = "Email is invalid"
        }
        return error
    }

    password(value, blacklist) {
        let error;
        if (!value) {
            error = "This field is required"
        }
        if (value.length < 8) {
            error = "Password must contain at least 8 characters"
        }

        // TODO Other password integrity checks

        blacklist.forEach(item => {
            if (value.toLowerCase().includes(item.toLowerCase())) {
                error = "Password must not contain personal information"
            }
        })

        return error
    }

    require(value, min, max) {
        let error;
        if (!value) {
            error = "This field is required"
        }
        if (min) {
            if (value.length < min) {
                error = `At least ${min} characters are required`
            }
        }
        if (max) {
            if (value.length > max) {
                error = `No more than ${max} characters are allowed`
            }
        }
        return error
    }

    confirmation(value, compare) {
        let error
        if (!value) {
            error = 'Confirmation is required'
        }
        if (value !== compare) {
            error = 'Confirmation does not match'
        }
        return error
    }
}

export default new validate()
