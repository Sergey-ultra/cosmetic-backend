 const    prepareQueryParams =   (tableOptions, filter) => {
    let params = {}

     for (let key in filter) {
         if (!['null', ''].includes(filter[key].value)) {
             if (filter[key].type === 'like') {
                 params[`filter[${key}][like]`] = filter[key].value
             } else if (filter[key].type === 'strong') {
                 params[`filter[${key}]`] = filter[key].value
             }
         }
     }


     Object.assign(params,{
         page:  tableOptions.page,
         per_page: tableOptions.perPage
     })



     if (tableOptions.sortBy) {
         let sortBy =  tableOptions.sortBy
         if (tableOptions.sortDesc) {
             sortBy = '-' + sortBy
         }
         params.sort = sortBy
     }


    return params
}

export  default prepareQueryParams

