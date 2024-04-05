interface Server {
  id: number
  model: string
  ram: string
  hdd: string
  location: string
  price: string
}

interface Meta {
  totalItems: number
  itemsPerPage: number
  currentPage: number
}

type SortState = {
  [key: string]: number
}
