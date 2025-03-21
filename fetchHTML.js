// api 1: https://chrome.browserless.io/?token=RpcOStJlFlhovqd536de1a84754fe4ff6d235b6cb5
// api 2: https://chrome.browserless.io/?token=RyjmmlY8n9tO3644a9cc84de902c05f4ba261502c0
const puppeteer = require('puppeteer-core');

async function getPageHTML(url) {
    const browser = await puppeteer.connect({
        browserWSEndpoint: 'wss://chrome.browserless.io?token=RyjmmlY8n9tO3644a9cc84de902c05f4ba261502c0'
    });

    const page = await browser.newPage();

    try {
        await page.setDefaultNavigationTimeout(60000);
        await page.goto(url, { waitUntil: ['networkidle2'] });
        await new Promise(resolve => setTimeout(resolve, 3000));

        const html = await page.content();
        return html;

    } catch (error) {
        console.error('Error:', error);
        throw error;
    } finally {
        await browser.close();
    }
}

const [url] = process.argv.slice(2);
if (!url) {
    process.exit(1);
}

getPageHTML(url)
    .then(html => console.log(html))
    .catch(error => {
        console.error(error);
        process.exit(1);
    });












// const puppeteer = require('puppeteer');

// async function getPageHTML(url) {
//     const browser = await puppeteer.launch({
//         headless: "new",
//         args: ['--no-sandbox']
//     });


//     const page = await browser.newPage();

//     try {
//         await page.setDefaultNavigationTimeout(60000);

//         await page.goto(url, {
//             waitUntil: ['networkidle2']
//         });
//         await new Promise(resolve => setTimeout(resolve, 3000));

//         const html = await page.content();
//         return html;

//     } catch (error) {
//         console.error('Final error:', error);
//         throw error;
//     } finally {
//         await browser.close();
//     }
// }

// const [url] = process.argv.slice(2);
// if (!url) {
//     process.exit(1);
// }

// getPageHTML(url)
//     .then(html => {
//         console.log(html);
//     })
//     .catch(error => {
//         console.error(error);
//         process.exit(1);
//     });
