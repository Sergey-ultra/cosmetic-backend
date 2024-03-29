let validation = {
    methods: {
        isUrl(string) {
            let url
            try {
                url = new URL(string);
            } catch (e) {
                return false;
            }

            return url.protocol === "http:" || url.protocol === "https:";
        }
    }
}