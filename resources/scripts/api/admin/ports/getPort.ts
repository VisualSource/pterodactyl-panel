import http from '@/api/http';
import { Port, rawDataToPort } from '@/api/admin/ports/getPorts';

export default (id: number, include: string[] = []): Promise<Port> => {
    return new Promise((ok,rej)=>{
        http.get(`/api/application/ports/${id}`,{
            params: { include: include.join(",") }
        }).then(({data})=>ok(rawDataToPort(data))).catch(rej);
    });
}
