export default function() {
  let localSto = localStorage.getItem("logueado");
  if (localSto) {
    localSto = JSON.parse(localSto);

    let arrayMenu = [
      {
        title: "General",
        htmlBefore: '<i class="material-icons">move_up</i>',
        to: "/general"
      },
      {
        title: "Ventas",
        htmlBefore: '<i class="material-icons">attach_money</i>',
        to: "/ventas"
      },
      {
        title: "Clientes",
        htmlBefore: '<i class="material-icons">person</i>',
        to: "/clientes"
      },

      {
        title: "Vendedores",
        htmlBefore: '<i class="material-icons">person_outline</i>',
        to: "/vendedores"
      },
      {
        title: "Stock",
        htmlBefore: '<i class="material-icons">phone_iphone</i>',
        to: "/productos"
      },
      {
        title: "Marcas",
        htmlBefore: '<i class="material-icons">sell</i>',
        to: "/marcas"
      },

      {
        title: "Proveedores",
        htmlBefore: '<i class="material-icons">person_pin</i>',
        to: "/proveedores"
      },
      {
        title: "Cuentas",
        htmlBefore: '<i class="material-icons">account_balance</i>',
        to: "/cuentas"
      }
      // {
      //   title: "Blog Dashboard",
      //   to: "/blog-overview",
      //   htmlBefore: '<i class="material-icons">edit</i>',
      //   htmlAfter: ""
      // },
      // {
      //   title: "User Profile",
      //   htmlBefore: '<i class="material-icons">person</i>',
      //   to: "/user-profile-lite"
      // }
    ];

    //Si es admin, agrego al arrayMenu los otros menues.
    if (localSto.rol_id === 1) {
      arrayMenu.push(
        {
          title: "Compras",
          htmlBefore: '<i class="material-icons">shopping_cart</i>',
          to: "/compras"
        },
        {
          title: "Caja Pesos",
          htmlBefore: '<i class="material-icons">paid</i>',
          to: "/caja-pesos"
        },
        {
          title: "Caja Bille",
          htmlBefore: '<i class="material-icons">paid</i>',
          to: "/caja-bille"
        },
        {
          title: "Roles y permisos",
          htmlBefore: '<i class="material-icons">key</i>',
          to: "/roles-permisos"
        }
      );
    }

    return arrayMenu;
  }
}
