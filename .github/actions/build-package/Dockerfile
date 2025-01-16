FROM alpine:3.10

ENV GITHUB_WORKSPACE=/github/workspace
ENV GITHUB_OUTPUT=/dev/null

RUN mkdir -p "$GITHUB_WORKSPACE" && \
    apk add --no-cache bash git rsync jq outils-md5

COPY composer.sample.json /composer.sample.json
COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]