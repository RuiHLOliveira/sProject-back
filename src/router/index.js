import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import ListaContas from '../views/ListaContas.vue'
import ListaMovimentos from '../views/ListaMovimentos.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/listaContas',
    name: 'listaContas',
    component: ListaContas
  },
  {
    path: '/listaMovimentos/:idConta',
    name: 'listaMovimentos',
    component: ListaMovimentos,
    props: true
  }
]

const router = new VueRouter({
  routes
})

export default router
