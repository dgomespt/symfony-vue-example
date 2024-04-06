
export interface Server {
  id: number
  model: string
  ram: string
  hdd: string
  location: string
  price: string
}

export type Meta = {
  totalItems?: number
  itemsPerPage: number
  currentPage: number
}

export type SortStates = {
  [key: string]: number
}
