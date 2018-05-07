//Slugify text
function doSlugify(text, spaceCharacter)
{
    return text.toString().toLowerCase()
            .replace(/\s+/g, spaceCharacter)           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, spaceCharacter)         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
}
