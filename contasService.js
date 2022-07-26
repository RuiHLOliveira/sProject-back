
async function buscarContas(forcePromise = false) {
    const url = 'http://localhost:8000/contas';
    const config = {
        method: 'GET'
    }
    if(forcePromise == false)
        return await buscarContasPromise (url, config);
    else
        return buscarContasPromise(url, config);
}

function buscarContasPromise(url, config){
    return fetch(url, config)
    .then(async(response) => {
        let text = await response.text()
        let dados = JSON.parse(text);
        return dados
    })
    .catch((error) => {
        console.error(error);
    });
}