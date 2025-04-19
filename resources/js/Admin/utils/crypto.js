const dec2hex = dec => dec.toString(16).padStart(2, "0");


// generateId :: Integer -> String
const generateId  = len => {
    let arr = new Uint8Array((len || 40) / 2)
    window.crypto.getRandomValues(arr)
    return Array.from(arr, dec2hex).join('')
}
export default generateId
