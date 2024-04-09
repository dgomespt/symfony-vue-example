
export interface Server {
  id: number
  model: string
  ram: string
  hdd: string
  location: string
  price: string
}

export type Meta = {
  [key: string]: string
}

export type RequestParams = {
  page: number
  itemsPerPage?: number,
  filters?: {
    [key: string]: string
  },
  order?: {
    [key: string]: string
  }
}