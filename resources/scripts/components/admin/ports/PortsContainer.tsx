import type { ChangeEvent } from 'react';
import { useContext, useEffect } from 'react';
import { NavLink } from 'react-router-dom';
import useFlash from '@/plugins/useFlash';
import CopyOnClick from '@/components/elements/CopyOnClick';
import AdminTable, {
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
    Pagination,
    Loading,
    NoItems,
    ContentWrapper,
    useTableHooks,
} from '@/components/admin/AdminTable';
import AdminCheckbox from '@/components/admin/AdminCheckbox';
import tw from 'twin.macro';
import getPorts, { type Filters, Context as PortsContext } from '@/api/admin/ports/getPorts';
import AdminContentBlock from '@/components/admin/AdminContentBlock';
import FlashMessageRender from '@/components/FlashMessageRender';
import { AdminContext } from '@/state/admin';
import Button from '@/components/elements/Button';

function RowCheckbox({ id }: { id: number }) {
    const isChecked = AdminContext.useStoreState(state => state.ports.selectedPorts.indexOf(id) >= 0);
    const appendSelectedLocation = AdminContext.useStoreActions(actions => actions.ports.appendSelectedPort);
    const removeSelectedLocation = AdminContext.useStoreActions(actions => actions.ports.removeSelectedPort);

    return (
        <AdminCheckbox
            name={id.toString()}
            checked={isChecked}
            onChange={(e: ChangeEvent<HTMLInputElement>) => {
                if (e.currentTarget.checked) {
                    appendSelectedLocation(id);
                } else {
                    removeSelectedLocation(id);
                }
            }}
        />
    );
}

function PortsContainer() {
    const { page, setPage, setFilters, sort, setSort, sortDirection } = useContext(PortsContext);
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data: ports, error, isValidating } = getPorts();

    useEffect(() => {
        if (!error) {
            clearFlashes('ports');
            return;
        }

        clearAndAddHttpError({ key: 'ports', error });
    }, [error]);

    const length = ports?.items?.length || 0;
    const setSelectedPorts = AdminContext.useStoreActions(actions => actions.ports.setSelectedPorts);
    const selectedPortsLength = AdminContext.useStoreState(state => state.ports.selectedPorts.length);

    const onSelectAllClick = (e: ChangeEvent<HTMLInputElement>) => {
        setSelectedPorts(e.currentTarget.checked ? ports?.items?.map(location => location.id) || [] : []);
    };

    const onSearch = (query: string): Promise<void> => {
        return new Promise(resolve => {
            if (query.length < 2) {
                setFilters(null);
            } else {
                setFilters({ method: query, type: query, external_port: query });
            }
            return resolve();
        });
    };

    useEffect(() => {
        setSelectedPorts([]);
    }, [page]);

    return (
        <AdminContentBlock title="Ports">
            <div css={tw`w-full flex flex-row items-center mb-8`}>
                <div css={tw`flex flex-col flex-shrink`} style={{ minWidth: '0' }}>
                    <h2 css={tw`text-2xl text-neutral-50 font-header font-medium`}>Ports</h2>
                    <p css={tw`text-base text-neutral-400 whitespace-nowrap overflow-ellipsis overflow-hidden`}>
                        All ports that allocations can be assigned to for easier categorization.
                    </p>
                </div>

                <div css={tw`flex ml-auto pl-4`}>
                    <NavLink to={`/admin/ports/new`}>
                        <Button type="button" size="large" css={tw`h-10 px-4 py-0 whitespace-nowrap`}>
                            New Port
                        </Button>
                    </NavLink>
                </div>
            </div>

            <FlashMessageRender byKey={'ports'} css={tw`mb-4`} />

            <AdminTable>
                <ContentWrapper
                    checked={selectedPortsLength === (length === 0 ? -1 : length)}
                    onSelectAllClick={onSelectAllClick}
                    onSearch={onSearch}
                >
                    <Pagination data={ports} onPageSelect={setPage}>
                        <div css={tw`overflow-x-auto`}>
                            <table css={tw`w-full table-auto`}>
                                <TableHead>
                                    <TableHeader
                                        name="ID"
                                        direction={sort === 'id' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('id')}
                                    />
                                    <TableHeader
                                        name="External Port"
                                        direction={sort === 'external_port' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('external_port')}
                                    />
                                    <TableHeader name="Description" />
                                    <TableHeader
                                        name="Type"
                                        direction={sort === 'type' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('type')}
                                    />
                                    <TableHeader
                                        name="Method"
                                        direction={sort === 'method' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('method')}
                                    />
                                </TableHead>

                                <TableBody>
                                    {ports !== undefined &&
                                        !error &&
                                        !isValidating &&
                                        length > 0 &&
                                        ports.items.map(port => (
                                            <TableRow key={port.id}>
                                                <td css={tw`pl-6`}>
                                                    <RowCheckbox id={port.id} />
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    <CopyOnClick text={port.id.toString()}>
                                                        <code css={tw`font-mono bg-neutral-900 rounded py-1 px-2`}>
                                                            {port.id}
                                                        </code>
                                                    </CopyOnClick>
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    <NavLink
                                                        to={`/admin/ports/${port.id}`}
                                                        css={tw`text-primary-400 hover:text-primary-300`}
                                                    >
                                                        {port.external_port.toString()}
                                                    </NavLink>
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    {port.description ?? 'Pterodactyl Port'}
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    {port.type}
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    {port.method}
                                                </td>
                                            </TableRow>
                                        ))}
                                </TableBody>
                            </table>

                            {ports === undefined || (error && isValidating) ? (
                                <Loading />
                            ) : length < 1 ? (
                                <NoItems />
                            ) : null}
                        </div>
                    </Pagination>
                </ContentWrapper>
            </AdminTable>
        </AdminContentBlock>
    );
}

export default () => {
    const hooks = useTableHooks<Filters>();
    return (
        <PortsContext.Provider value={hooks}>
            <PortsContainer />
        </PortsContext.Provider>
    );
};
