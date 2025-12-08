import type Post from "./Post";
import type { NewPost } from "./Post";

export class PostServiceError extends Error {
    constructor(message: string) {
        super(message);
        this.name = 'PostServiceError';
    }
}

export default class PostService {

    private posts: Post[] = [];

    constructor() {
        this.load();
    }

    public get(): Post[] {
        return this.posts;
    }

    public getById(id: number): Post {
        let post = this.posts.find(p => p.id === id);
        if (!post) {
            throw new PostServiceError('Post not found');
        }
        return post;
    }

    public create(post: NewPost): Post {
        let newPost = {
            id: this.posts.length + 1,
            ...post
        } as Post;

        this.posts.push(newPost);
        this.persist();

        return newPost;
    }

    public delete(id: number): void {
        let index = this.posts.findIndex(p => p.id === id);
        if (index === -1) {
            throw new PostServiceError('Post not found');
        }

        this.posts.splice(index, 1);
        this.persist();
    }

    private persist(): void {
        localStorage.setItem('posts', JSON.stringify(this.posts));
    }

    private load(): void {
        const data = localStorage.getItem('posts');
        if (data) {
            this.posts = JSON.parse(data);
        } else {
            this.posts = [
                {
                    id: 1,
                    title: 'Why TypeScript is Essential for React Projects',
                    content: 'Using TypeScript with React brings the benefits of static typing, helping developers catch bugs early and reducing runtime errors. By defining types for props, state, and functions, TypeScript improves code quality and makes collaboration easier, especially on larger teams. The enhanced autocompletion and refactoring tools in TypeScript-compatible IDEs also boost productivity, making it a great choice for robust React projects.'
                },
                {
                    id: 2,
                    title: '3 Tips to Improve Frontend Performance',
                    content: 'Optimizing frontend performance enhances user experience and boosts SEO. Start by minimizing and compressing images, which can greatly reduce load times. Next, leverage code-splitting in tools like Webpack to load only the JavaScript needed for each page, improving initial load speeds. Finally, take advantage of caching techniques, such as service workers and HTTP caching, to ensure repeat visits are faster, resulting in a smoother and more efficient experience for users.'
                }
            ];
            this.persist();
        }
    }

}
