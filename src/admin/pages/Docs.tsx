import { useEffect, useRef, useState, type DragEvent } from 'react';
import { Link as RouterLink, Route, Routes, useParams } from 'react-router-dom';
import { BackButton, Layouts, Page, useFetchClient, useNotification } from '@strapi/strapi/admin';
import { Box, EmptyStateLayout, Flex, LinkButton, Typography } from '@strapi/design-system';
import { ChevronRight, Drag, Pencil, Plus } from '@strapi/icons';
import { EmptyDocuments } from '@strapi/icons/symbols';
import { BlocksContent } from '../components/BlocksContent';

const DOC_UID = 'api::doc.doc';
const CM_PATH = `/content-manager/collection-types/${DOC_UID}`;
const DOCS_API = '/ptsd-admin/docs';

type Doc = { documentId: string; title: string; content?: any[] | null; updatedAt: string };

const formatDate = (iso: string) => new Date(iso).toLocaleDateString('ro-RO', { dateStyle: 'medium' });

/** Moves the item at `from` to `to`, shifting the ones in between. */
const move = <T,>(items: T[], from: number, to: number) => {
  const next = [...items];
  next.splice(to, 0, ...next.splice(from, 1));
  return next;
};

const CreateButton = () => (
  <LinkButton tag={RouterLink} to={`${CM_PATH}/create`} startIcon={<Plus />}>
    Adaugă doc
  </LinkButton>
);

const DocsList = () => {
  const { get, put } = useFetchClient();
  const { toggleNotification } = useNotification();
  const [docs, setDocs] = useState<Doc[] | null>(null);
  const [failed, setFailed] = useState(false);
  const [dragging, setDragging] = useState<string | null>(null);
  const orderBeforeDrag = useRef<Doc[]>([]);

  useEffect(() => {
    get<{ docs: Doc[] }>(DOCS_API)
      .then(({ data }) => setDocs(data.docs))
      .catch(() => setFailed(true));
  }, []);

  if (failed) return <Page.Error />;
  if (!docs) return <Page.Loading />;

  const startDrag = (event: DragEvent, documentId: string) => {
    event.dataTransfer.effectAllowed = 'move';
    orderBeforeDrag.current = docs;
    setDragging(documentId);
  };

  // The list reorders live while the dragged row passes over the others.
  const dragOver = (event: DragEvent, documentId: string) => {
    event.preventDefault();
    if (!dragging || dragging === documentId) return;
    const from = docs.findIndex((doc) => doc.documentId === dragging);
    const to = docs.findIndex((doc) => doc.documentId === documentId);
    setDocs(move(docs, from, to));
  };

  const endDrag = async () => {
    setDragging(null);
    const previous = orderBeforeDrag.current;
    if (previous.every((doc, i) => doc.documentId === docs[i]?.documentId)) return;
    try {
      await put(`${DOCS_API}/order`, { documentIds: docs.map((doc) => doc.documentId) });
      toggleNotification({ type: 'success', message: 'Ordinea a fost salvată.' });
    } catch {
      setDocs(previous);
      toggleNotification({ type: 'danger', message: 'Nu am putut salva ordinea.' });
    }
  };

  return (
    <Page.Main>
      <Page.Title>Docs</Page.Title>
      <Layouts.Header
        title="Docs"
        subtitle="Cum funcționează Strapi împreună cu aplicația mobilă"
        primaryAction={<CreateButton />}
      />
      <Layouts.Content>
        {docs.length === 0 ? (
          <EmptyStateLayout
            icon={<EmptyDocuments width="16rem" />}
            content="Nu există încă niciun doc."
            action={<CreateButton />}
          />
        ) : (
          <Flex direction="column" alignItems="stretch" gap={2}>
            {docs.map((doc) => (
              <Flex
                key={doc.documentId}
                draggable
                onDragStart={(event: DragEvent) => startDrag(event, doc.documentId)}
                onDragOver={(event: DragEvent) => dragOver(event, doc.documentId)}
                onDragEnd={endDrag}
                background="neutral0"
                hasRadius
                shadow="tableShadow"
                style={{ opacity: dragging === doc.documentId ? 0.5 : 1 }}
              >
                <Box padding={5} paddingRight={2} style={{ cursor: 'grab', display: 'flex' }} title="Trage ca să schimbi ordinea">
                  <Drag />
                </Box>
                <Box
                  tag={RouterLink}
                  to={doc.documentId}
                  draggable={false}
                  flex={1}
                  padding={5}
                  paddingLeft={2}
                  style={{ textDecoration: 'none' }}
                >
                  <Flex justifyContent="space-between">
                    <Flex direction="column" alignItems="flex-start" gap={1}>
                      <Typography variant="delta" textColor="neutral800">
                        {doc.title}
                      </Typography>
                      <Typography variant="pi" textColor="neutral600">
                        Actualizat {formatDate(doc.updatedAt)}
                      </Typography>
                    </Flex>
                    <ChevronRight />
                  </Flex>
                </Box>
              </Flex>
            ))}
          </Flex>
        )}
      </Layouts.Content>
    </Page.Main>
  );
};

const DocView = () => {
  const { documentId } = useParams<{ documentId: string }>();
  const { get } = useFetchClient();
  const [doc, setDoc] = useState<Doc | null>(null);
  const [failed, setFailed] = useState(false);

  useEffect(() => {
    get<{ data: Doc }>(`${CM_PATH}/${documentId}`)
      .then(({ data }) => setDoc(data.data))
      .catch(() => setFailed(true));
  }, [documentId]);

  if (failed) return <Page.Error />;
  if (!doc) return <Page.Loading />;

  return (
    <Page.Main>
      <Page.Title>{doc.title}</Page.Title>
      <Layouts.Header
        title={doc.title}
        subtitle={`Actualizat ${formatDate(doc.updatedAt)}`}
        navigationAction={<BackButton fallback="/docs" />}
        primaryAction={
          <LinkButton tag={RouterLink} to={`${CM_PATH}/${doc.documentId}`} variant="secondary" startIcon={<Pencil />}>
            Editează
          </LinkButton>
        }
      />
      <Layouts.Content>
        <Box background="neutral0" padding={8} hasRadius shadow="tableShadow">
          {doc.content?.length ? (
            <BlocksContent content={doc.content} />
          ) : (
            <Typography textColor="neutral600">Doc-ul nu are conținut.</Typography>
          )}
        </Box>
      </Layouts.Content>
    </Page.Main>
  );
};

const DocsPage = () => (
  <Routes>
    <Route index element={<DocsList />} />
    <Route path=":documentId" element={<DocView />} />
  </Routes>
);

export default DocsPage;
