
async function buscarTiposMovimentos() {
    const url = 'http://localhost:8000/tiposmovimentos';
    const config = {
        method: 'GET'
    }
    return await fetch(url, config)
    .then(async(response) => {
        let text = await response.text()
        let dados = JSON.parse(text);
        return dados
    })
    .catch((error) => {
        console.error(error);
    });
}